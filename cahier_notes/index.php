<?php
/**
 * Arborescence des évaluations
 * 
 *
 * @copyright Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Carnet_de_notes
 * @subpackage Conteneur
 * @license GNU/GPL 
 * @see affiche_devoirs_conteneurs()
 * @see check_token()
 * @see checkAccess()
 * @see get_groups_for_prof()
 * @see getSettingValue()
 * @see mise_a_jour_moyennes_conteneurs()
 * @see recopie_arbo()
 * @see Session::security_check()
 * @see traitement_magic_quotes()
 * @see tentative_intrusion()
 * @see Verif_prof_cahier_notes()
 */

/* This file is part of GEPI.
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

/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"HTTP_REFERER=".$_SERVER['HTTP_REFERER']."\n");
fwrite($fich,"\$_SERVER['REQUEST_URI']=".$_SERVER['REQUEST_URI']."\n");
fwrite($fich,date("Y m d - H i s")."\n");
//fwrite($fich,"==================================\n");
fclose($fich);
*/
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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

//debug_var();

// 20241019
$GLOBALS['dont_get_modalite_elect']=true;

$msg="";

// 20210301
/*
$tab_id_classe_exclues_module_cahier_notes=array();
if(!getSettingAOui('acces_cn_prof')) {
	$tab_id_classe_exclues_module_cahier_notes=get_classes_exclues_tel_module('cahier_notes');
}
*/

unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);

// 20210301
$pref_acces_cn_prof_afficher_lien=getPref($_SESSION['login'], 'acces_cn_prof_afficher_lien','');
if((!getSettingAOui('acces_cn_prof'))||($pref_acces_cn_prof_afficher_lien!='y')) {
	if((isset($id_groupe))&&(preg_match('/^[0-9]{1,}$/', $id_groupe))&&(is_groupe_exclu_tel_module($id_groupe, 'cahier_notes'))) {
		$id_groupe="no_group";
		unset($_GET['periode_num']);
		unset($_POST['periode_num']);
	}
}

if ($id_groupe == "no_group") {
	$id_groupe = NULL;
	unset($_GET['id_groupe']);
	$_SESSION['id_groupe_session'] = "";
}

//on met le groupe dans la session, pour naviguer entre absence, cahier de texte et autres
if ($id_groupe != NULL) {
	$_SESSION['id_groupe_session'] = $id_groupe;
} else if (isset($_SESSION['id_groupe_session']) && $_SESSION['id_groupe_session'] != "") {
	// 20210301
	if((preg_match('/^[0-9]{1,}$/', $_SESSION['id_groupe_session']))&&(is_groupe_exclu_tel_module($_SESSION['id_groupe_session'], 'cahier_notes'))) {
		$id_groupe = NULL;
		unset($_GET['id_groupe']);
		$_SESSION['id_groupe_session'] = "";
	}
	else {
		$_GET['id_groupe'] = $_SESSION['id_groupe_session'];
		$id_groupe = $_SESSION['id_groupe_session'];
	}
}

if (is_numeric($id_groupe) && $id_groupe > 0) {

	$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='".$_SESSION['login']."';";
	$test_prof_groupe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_prof_groupe)==0) {
		$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
		unset($_SESSION['id_groupe_session']);
		tentative_intrusion(1, "Tentative d'accès à un carnet de notes qui ne lui appartient pas (id_groupe=$id_groupe)");
		// Sans le unset($_SESSION['id_groupe_session']) avec ces tentative_intrusion(), une modif d'id_groupe en barre d'adresse me provoquait 7 insertions... d'où un score à +7 et une déconnexion
		header("Location: index.php?msg=$mess");
		die();
	}

	$current_group = get_group($id_groupe);
}

// On teste si le carnet de notes appartient bien à la personne connectée
if ((isset($_POST['id_racine'])) or (isset($_GET['id_racine']))) {
	$id_racine = isset($_POST['id_racine']) ? $_POST['id_racine'] : (isset($_GET['id_racine']) ? $_GET['id_racine'] : NULL);
	if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
		$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
		unset($_SESSION['id_groupe_session']);
		tentative_intrusion(1, "Tentative d'accès à un carnet de notes qui ne lui appartient pas (id_racine=$id_racine)");
		header("Location: index.php?msg=$mess");
		die();
	}

	// 20180515 : Contrôler qu'il y a cohérence id_racine et id_groupe
	//            Si ça ne colle pas, forcer id_groupe et recalculer current_group
}

if(
	(isset($id_groupe))&&
	(isset($current_group))&&
	(isset($id_racine))&&
	(isset($_GET['id_dev']))&&
	(preg_match("/^[0-9]{1,}$/", $_GET['id_dev']))&&
	(isset($_GET['mode']))&&
	($_GET['mode']=='change_visibilite_dev')&&
	(isset($_GET['visible']))&&
	(($_GET['visible']=='y')||($_GET['visible']=='n'))
) {
	check_token();

	$sql="SELECT periode FROM cn_cahier_notes WHERE id_cahier_notes='$id_racine';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$tmp_periode_num=$lig->periode;

		$acces_exceptionnel_saisie=false;
		if((isset($periode_num))&&($_SESSION['statut']=='professeur')) {
			$acces_exceptionnel_saisie=acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $tmp_periode_num);
		}

		if (($current_group["classe"]["ver_periode"]["all"][$tmp_periode_num] >= 2)||($acces_exceptionnel_saisie)) {
			$sql="SELECT * FROM cn_devoirs WHERE id_racine='$id_racine' AND id='".$_GET['id_dev']."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				$date_dev=$lig->date;
				$date_ele_resp_dev=$lig->date_ele_resp;

				$sql="UPDATE cn_devoirs SET display_parents='".(($_GET['visible']=='y') ? 1 : 0)."' WHERE id_racine='$id_racine' AND id='".$_GET['id_dev']."';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);

				if((isset($_GET['mode_js']))&&
				($_GET['mode_js']=='y')) {
					if(!$update) {
						echo "<span style='color:red'>ERREUR</span>";
					}
					else {
						if($_GET['visible']=='y') {
							echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=".$_GET['id_dev']."&amp;mode=change_visibilite_dev&amp;visible=n".add_token_in_url()."' onclick=\"change_visibilite_dev(".$_GET['id_dev'].",'n');return false;\"><img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du ".formate_date($date_dev)." visible sur le relevé de notes.
Visible à compter du ".formate_date($date_ele_resp_dev)." pour les parents et élèves.

Cliquez pour ne pas faire apparaître cette note sur le relevé de notes.' alt='Evaluation visible sur le relevé de notes' /></a>";
						}
						else {
							echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=".$_GET['id_dev']."&amp;mode=change_visibilite_dev&amp;visible=y".add_token_in_url()."' onclick=\"change_visibilite_dev(".$_GET['id_dev'].",'y');return false;\"><img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes.
					
Cliquez pour faire apparaître cette note sur le relevé de notes.' alt='Evaluation non visible sur le relevé de notes' /></a>\n";
						}
					}

					die();
				}
				else {
					// On va poursuivre et afficher le $msg
					if(!$update) {
						$msg="Erreur lors de la modification de la visbilité du devoir n°".$_GET['id_dev']."<br />";
					}
					else {
						$msg="Visbilité du devoir n°".$_GET['id_dev']." modifiée.<br />";
					}
				}
			}
		}
	}
}

if(isset($_GET['clean_anomalie_cn'])) {
	check_token();

	$suppr_id_dev=$_GET['suppr_id_dev'];
	if(preg_match('/^[0-9]*$/', $suppr_id_dev)) {
		$sql="SELECT 1=1 FROM cn_devoirs cd, cn_cahier_notes ccn, j_groupes_professeurs jgp WHERE cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe=jgp.id_groupe AND jgp.login='".$_SESSION['login']."' AND cd.id='".$suppr_id_dev."';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$sql="DELETE FROM cn_notes_devoirs WHERE id_devoir='".$suppr_id_dev."';";
			$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$suppr) {
				$msg.="Erreur lors de la suppression des notes associées au devoir n°$suppr_id_dev.<br />";
			}
			else {
				$sql="DELETE FROM cn_devoirs WHERE id='".$suppr_id_dev."';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$suppr) {
					$msg.="Erreur lors de la suppression du devoir n°$suppr_id_dev.<br />";
				}
				else {
					$msg.="Devoir n°$suppr_id_dev supprimé.<br />";
				}
			}
		}
		else {
			$msg.="Vous tentez de supprimer un devoir qui ne vous appartient pas.<br />";
		}
	}
}

if(isset($_GET['clean_anomalie_dev'])) {

	if((isset($_GET['id_groupe']))&&(isset($_GET['periode_num']))) {
		$tmp_id_groupe=$_GET['id_groupe'];
		$tmp_periode_num=$_GET['periode_num'];
		//echo "A<br />";
	}
	elseif(isset($_GET['id_racine'])) {
		$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes ='".$_GET['id_racine']."';");
		$tmp_id_groupe = old_mysql_result($appel_cahier_notes, 0, 'id_groupe');
		$tmp_periode_num = old_mysql_result($appel_cahier_notes, 0, 'periode');
	}

	if((!isset($tmp_id_groupe))||(!isset($tmp_periode_num))) {
		$msg="Le groupe ou la période ne sont pas définis.<br />";
	}
	else {
		$sql="SELECT ccn.id_groupe, ccn.periode FROM cn_cahier_notes ccn, 
								cn_conteneurs cc, 
								cn_devoirs cd
				WHERE ccn.id_cahier_notes=cc.id_racine AND 
						ccn.id_groupe='$tmp_id_groupe' AND
						ccn.periode='$tmp_periode_num' AND
						cc.id=cd.id_conteneur AND
						cd.id='".$_GET['clean_anomalie_dev']."';";
		//echo "$sql<br />";
		$test_cn=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_cn)==0) {
			$msg="Tentative d'accès à un devoir non associé à un de vos carnet de notes.<br />";
		}
		else {
			$lig_tmp=mysqli_fetch_object($test_cn);
			$sql="SELECT * FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='".$_GET['clean_anomalie_dev']."' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$tmp_periode_num' AND jec.login not in (select login from j_eleves_groupes where id_groupe='$tmp_id_groupe' and periode='$tmp_periode_num');";
			//echo "$sql<br />";
			$res_a=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_a)==0) {
				$msg="Aucune anomalie n'est relevée pour le devoir n°".$_GET['clean_anomalie_dev'].".<br />";
			}
			else {
				if(!isset($msg)) {$msg="";}
				while($lig_a=mysqli_fetch_object($res_a)) {
					$sql="DELETE FROM cn_notes_devoirs WHERE id_devoir='".$_GET['clean_anomalie_dev']."' AND login NOT IN (select login from j_eleves_groupes where id_groupe='$tmp_id_groupe' and periode='$tmp_periode_num');";
					//echo "$sql<br />";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if($del) {
						$msg.="Ménage effectué pour $lig_a->login : $lig_a->note - $lig_a->statut sur le devoir n°".$_GET['clean_anomalie_dev'].".<br />\n";
					}
					else {
						$msg.="Erreur lors du ménage pour $lig_a->login : $lig_a->note - $lig_a->statut sur le devoir n°".$_GET['clean_anomalie_dev'].".<br />\n";
					}
				}
			}
		}

	}
}
/**
 * 
 */
require('cc_lib.php');

//**************** EN-TETE *****************
$titre_page = "Carnet de notes";
/**
 * Entête de la page
 */
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"Juste après Header\n");
//fwrite($fich,"==================================\n");
fclose($fich);
*/

//debug_var();

// 20210302
$pref_acces_cn_prof_afficher_lien=getPref($_SESSION['login'], 'acces_cn_prof_afficher_lien','');

//-----------------------------------------------------------------------------------
if (isset($_REQUEST['id_devoir'])) {
    $appel_devoir = mysqli_query($GLOBALS["mysqli"], "SELECT id_racine FROM cn_devoirs WHERE (id='".$_REQUEST['id_devoir']."')");
    if (mysqli_num_rows($appel_devoir) != 0) {
    	$id_racine = old_mysql_result($appel_devoir, 0, 'id_racine');
    }
}

if (isset($_GET['id_groupe']) and isset($_GET['periode_num'])) {
    $id_groupe = $_GET['id_groupe'];
    $periode_num = $_GET['periode_num'];
    $login_prof = $_SESSION['login'];

    if(!isset($current_group)) {$current_group = get_group($id_groupe);}
	// Avec des classes qui n'ont pas le même nombre de période, on peut arriver avec un periode_num impossible pour un id_groupe
	while((!isset($current_group["classe"]["ver_periode"]["all"][$periode_num]))&&($periode_num>0)) {
		$periode_num--;
	}
	if($periode_num<1) {
		$mess=rawurlencode("ERREUR: Aucune période n'a été trouvée pour le groupe choisi !");
		header("Location: index.php?msg=$mess");
		die();
	}

	// Avec des classes qui n'ont pas le même nombre de période, on peut arriver avec un periode_num impossible pour un id_groupe
	while((!isset($current_group["classe"]["ver_periode"]["all"][$periode_num]))&&($periode_num>0)) {
		$periode_num--;
	}
	if($periode_num<1) {
		$mess=rawurlencode("ERREUR: Aucune période n'a été trouvée pour le groupe choisi !");
		header("Location: index.php?msg=$mess");
		die();
	}

    $appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')");
    $nb_cahier_note = mysqli_num_rows($appel_cahier_notes);
    if ($nb_cahier_note == 0) {
        $nom_complet_matiere = $current_group["matiere"]["nom_complet"];
        $nom_court_matiere = $current_group["matiere"]["matiere"];
        $reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_conteneurs SET id_racine='', nom_court='".traitement_magic_quotes($current_group["description"])."', nom_complet='". traitement_magic_quotes($nom_complet_matiere)."', description = '', mode = '".getPref($_SESSION['login'], 'cnBoitesModeMoy', (getSettingValue('cnBoitesModeMoy')!="" ? getSettingValue('cnBoitesModeMoy') : 2))."', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
        if ($reg) {
            $id_racine = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
            $reg = mysqli_query($GLOBALS["mysqli"], "UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine'");
            $reg = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode_num', id_cahier_notes='$id_racine'");
        }
    } else {
        $id_racine = old_mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
    }
}

$acces_exceptionnel_saisie=false;
if((isset($periode_num))&&($_SESSION['statut']=='professeur')) {
	$acces_exceptionnel_saisie=acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num);
}

// Recopie de la structure de la periode précédente
if ((isset($_GET['creer_structure'])) and (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)||($acces_exceptionnel_saisie)) and (getSettingAOui('GepiPeutCreerBoitesProf'))) {
  check_token();

  function recopie_arbo($id_racine, $id_prec,$id_new) {
    global $vide;
    $query_cont = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs
    WHERE (
        id != id_racine and
        parent = '".$id_prec."'
        )");
    $nb_lignes = mysqli_num_rows($query_cont);
    $i = 0;
    while ($i < $nb_lignes) {
        $id_prec = old_mysql_result($query_cont,$i,'id');
        $val2 = old_mysql_result($query_cont,$i,'id_racine');
        $val3 = old_mysql_result($query_cont,$i,'nom_court');
        $val4 = old_mysql_result($query_cont,$i,'nom_complet');
        $val5 = old_mysql_result($query_cont,$i,'description');
        $val6 = old_mysql_result($query_cont,$i,'mode');
        $val7 = old_mysql_result($query_cont,$i,'coef');
        $val8 = old_mysql_result($query_cont,$i,'arrondir');
        $val9 = old_mysql_result($query_cont,$i,'ponderation');
        $val10 = old_mysql_result($query_cont,$i,'display_parents');
        $val11 = old_mysql_result($query_cont,$i,'display_bulletin');
        $val12 = old_mysql_result($query_cont,$i,'parent');
        $query_insert = mysqli_query($GLOBALS["mysqli"], "INSERT INTO cn_conteneurs
        set id_racine = '".$id_racine."',
        nom_court = '".traitement_magic_quotes($val3)."',
        nom_complet = '".traitement_magic_quotes($val4)."',
        description = '".traitement_magic_quotes($val5)."',
        mode = '".$val6."',
        coef = '".$val7."',
        arrondir = '".$val8."',
        ponderation = '".$val9."',
        display_parents = '".$val10."',
        display_bulletin = '".$val11."',
        parent = '".$id_new."' ");
        $vide = 'no';
        $id_new1 = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
        recopie_arbo($id_racine, $id_prec, $id_new1);
        $i++;
    }

  }

    $periode_num = $_GET['periode_num'];
    $id_cahier_prec = sql_query1("SELECT id_cahier_notes FROM cn_cahier_notes
    WHERE (
        id_groupe = '".$id_groupe."' and
        periode = '".($periode_num-1)."'
        )
    ");
    $vide = 'yes';
    recopie_arbo($id_racine,$id_cahier_prec,$id_racine);
    if ($vide == 'yes') {
		echo "<p><center><b><font color='red'>Structure vide : aucun";
		if(getSettingValue('gepi_denom_boite_genre')=="f") {$accord_f="e";} else {$accord_f="";}
		echo "$accord_f ";
		echo getSettingValue('gepi_denom_boite');
		echo " n'a été cré$accord_f dans le carnet de notes de la période précédente.</font></b></center></p><hr />";
	}
}
/*
$fich=fopen("/tmp/test_img.txt","a+");
fwrite($fich,"Avant isset(\$id_racine)\n");
fclose($fich);
*/
if  (isset($id_racine) and ($id_racine!='')) {
    $appel_conteneurs = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE id ='$id_racine'");
    $nom_court = old_mysql_result($appel_conteneurs, 0, 'nom_court');

    $appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_cahier_notes WHERE id_cahier_notes = '$id_racine'");
    $id_groupe = old_mysql_result($appel_cahier_notes, 0, 'id_groupe');
    if (!isset($current_group)) $current_group = get_group($id_groupe);
    $periode_num = old_mysql_result($appel_cahier_notes, 0, 'periode');
    include "../lib/periodes.inc.php";

	$acces_exceptionnel_saisie=false;
	if((isset($periode_num))&&($_SESSION['statut']=='professeur')) {
		$acces_exceptionnel_saisie=acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num);
	}

	/*
	$fich=fopen("/tmp/test_img.txt","a+");
	fwrite($fich,"Juste avant test suppr\n");
	//fwrite($fich,"\$_GET['del_dev']=".$_GET['del_dev']."\n");
	//fwrite($fich,"\$_GET['js_confirmed']=".$_GET['js_confirmed']."\n");
	fwrite($fich,"++++++++++++++++++++++++++\n");
	fclose($fich);
	*/

    //
    // Suppression d'une évaluation
    //
    if ((isset($_GET['del_dev'])) and ($_GET['js_confirmed'] ==1)) {
        $temp = $_GET['del_dev'];
		/*
		$fich=fopen("/tmp/test_img.txt","a+");
		fwrite($fich,"\$_GET['del_dev']=".$_GET['del_dev']."\n");
		fwrite($fich,"==================================\n");
		fclose($fich);
		*/
	    check_token();

		if (($current_group["classe"]["ver_periode"]["all"][$periode_num]==3)||($acces_exceptionnel_saisie)) {
			$sql0="SELECT id_conteneur FROM cn_devoirs WHERE id='$temp'";
			//echo "$sql0<br />";
			$sql= mysqli_query($GLOBALS["mysqli"], $sql0);
			if(mysqli_num_rows($sql)==0) {
				echo "<p style='color:red'>Le devoir $temp n'a pas été trouvé.</p>\n";
			}
			else {
				$id_cont = old_mysql_result($sql, 0, 'id_conteneur');

				if($current_group["classe"]["ver_periode"]["all"][$periode_num]!=3) {
					$sql="SELECT * FROM cn_devoirs WHERE id='$temp';";
					//echo "$sql<br />";
					$res_cd=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_cd)>0) {
						$lig_cd=mysqli_fetch_object($res_cd);
						$texte="Suppression du devoir n°$temp : ".$lig_cd->nom_court." (".$lig_cd->nom_complet.") du ".formate_date($lig_cd->date).".\n";
						$retour=log_modifs_acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $periode_num, $texte);
					}
				}

				$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_notes_devoirs WHERE id_devoir='$temp'");
				$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_devoirs WHERE id='$temp'");
		
				// On teste si le conteneur est vide
				$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_devoirs WHERE id_conteneur='$id_cont'");
				$nb_dev = mysqli_num_rows($sql);
				$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_conteneurs WHERE parent='$id_cont'");
				$nb_cont = mysqli_num_rows($sql);
				if (($nb_dev == 0) or ($nb_cont == 0)) {
					$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_cont'");
				}
		
				// On teste si le carnet de notes est vide
				$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_devoirs WHERE id_conteneur='$id_racine'");
				$nb_dev = mysqli_num_rows($sql);
				$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_conteneurs WHERE parent='$id_racine'");
				$nb_cont = mysqli_num_rows($sql);
				if (($nb_dev == 0) and ($nb_cont == 0)) {
					$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_racine'");
				} else {
					$arret = 'no';
					mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_racine,$arret);
				}
			}
		}
    }
    //
    // Supression d'un conteneur
    //
    if ((isset($_GET['del_cont'])) and ($_GET['js_confirmed'] ==1)) {
		check_token();

		if (($current_group["classe"]["ver_periode"]["all"][$periode_num]==3)||($acces_exceptionnel_saisie)) {
			$temp = $_GET['del_cont'];
			$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_devoirs WHERE id_conteneur='$temp'");
			$nb_dev = mysqli_num_rows($sql);
			$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_conteneurs WHERE parent='$temp'");
			$nb_cont = mysqli_num_rows($sql);
			if (($nb_dev != 0) or ($nb_cont != 0)) {
				echo "<script type=\"text/javascript\" language=\"javascript\">\n";
				echo 'alert("Impossible de supprimer une boîte qui n\'est pas vide !");\n';
				echo "</script>\n";
			} else {
				$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_conteneurs WHERE id='$temp'");
				$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$temp'");
				// On teste si le carnet de notes est vide
				$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_devoirs WHERE id_conteneur='$id_racine'");
				$nb_dev = mysqli_num_rows($sql);
				$sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_conteneurs WHERE parent='$id_racine'");
				$nb_cont = mysqli_num_rows($sql);
				if (($nb_dev == 0) and ($nb_cont == 0)) {
					$sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_racine'");
				} else {
					$arret = 'no';
					mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_racine,$arret);
				}
			}
		}
    }

	insere_lien_calendrier_crob("right");

	echo "<div class='norme'>\n";
	echo "<form enctype=\"multipart/form-data\" id= \"form1\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
	echo "<p class='bold'>\n";
	echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a> | \n";
	echo "<a href='index.php?id_groupe=no_group'> Mes enseignements </a> | \n";


//if(isset($current_group)) { echo "DEBUG 1 : ".$current_group['classlist_string']."<br />";}

if(($_SESSION['statut']=='professeur')||($_SESSION['statut']=='secours')) {
	if($_SESSION['statut']=='professeur') {
		$login_prof_groupe_courant=$_SESSION["login"];
	}
	else {
		$tmp_current_group=get_group($id_groupe);

		$login_prof_groupe_courant=$tmp_current_group["profs"]["list"][0];
	}

	$tab_groups = get_groups_for_prof($login_prof_groupe_courant,"classe puis matière");

	//if(isset($current_group)) { echo "DEBUG 2 : ".$current_group['classlist_string']."<br />";}

	$debug_group_prec_suiv="n";
	if(!empty($tab_groups)) {

		$chaine_options_classes="";

		$num_groupe=-1;

		$tmp_groups=array();
		for($loop=0;$loop<count($tab_groups);$loop++) {
			// 20210301
			if(!is_groupe_exclu_tel_module($tab_groups[$loop]["id"], 'cahier_notes')) {
				if((!isset($tab_groups[$loop]["visibilite"]["cahier_notes"]))||($tab_groups[$loop]["visibilite"]["cahier_notes"]=='y')) {
					$tmp_groups[]=$tab_groups[$loop];
				}
				elseif(get_cn_from_id_groupe_periode_num($tab_groups[$loop]['id'], $periode_num)!="") {
					$tab_anomalie_cn_pour_groupe_hors_cn[$tab_groups[$loop]['id']]=get_cn_from_id_groupe_periode_num($tab_groups[$loop]['id'], $periode_num);
				}
			}
		}

		$nb_groupes_suivies=count($tmp_groups);

		if($debug_group_prec_suiv=="y") {echo "<p>Groupe actuellement affiché : $id_groupe<br />";}
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		$chaine_info_debug_id_groupe="";
		for($loop=0;$loop<count($tmp_groups);$loop++) {

			if($debug_group_prec_suiv=="y") {echo "Groupe n°$loop dans la boucle : ".$tmp_groups[$loop]['id']."<br />";$chaine_info_debug_id_groupe=" (id_groupe : ".$tmp_groups[$loop]['id'].")";}

			if((!isset($tmp_groups[$loop]["visibilite"]["cahier_notes"]))||($tmp_groups[$loop]["visibilite"]["cahier_notes"]=='y')) {
				// On ne retient que les groupes qui ont un nombre de périodes au moins égal à la période sélectionnée
				if($tmp_groups[$loop]["nb_periode"]>=$periode_num) {
					if($tmp_groups[$loop]['id']==$id_groupe){
						$num_groupe=$loop;

						if($debug_group_prec_suiv=="y") {echo "Le groupe n°$loop dans la boucle est le groupe courant : ".$tmp_groups[$loop]['id']."<br />";}

						$chaine_options_classes.="<option value='".$tmp_groups[$loop]['id']."' selected='selected'>".htmlspecialchars($tmp_groups[$loop]['description'])." (".$tmp_groups[$loop]['classlist_string'].")$chaine_info_debug_id_groupe</option>\n";

						$temoin_tmp=1;
						if($debug_group_prec_suiv=="y") {echo "On teste \$tmp_groups[$loop+1]<br />";}
						if(isset($tmp_groups[$loop+1])){
							$id_grp_suiv=$tmp_groups[$loop+1]['id'];
							if($debug_group_prec_suiv=="y") {echo "\$id_grp_suiv=".$tmp_groups[$loop+1]['id']."<br />";}
						}
						else{
							$id_grp_suiv=0;
							if($debug_group_prec_suiv=="y") {echo "\$id_grp_suiv=0<br />";}
						}
					}
					else {
						$chaine_options_classes.="<option value='".$tmp_groups[$loop]['id']."'>".htmlspecialchars($tmp_groups[$loop]['description'])." (".$tmp_groups[$loop]['classlist_string'].")$chaine_info_debug_id_groupe</option>\n";
					}
	
					if($temoin_tmp==0){
						$id_grp_prec=$tmp_groups[$loop]['id'];
						if($debug_group_prec_suiv=="y") {echo "Le groupe précédent est temporairement le n°$loop dans la boucle : ".$tmp_groups[$loop]['id']."<br />";}
					}
				}
			}
		}

		// =================================

		if(($chaine_options_classes!="")&&($nb_groupes_suivies>1)) {

			echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_groupe').selectedIndex=$num_groupe;
			}
		}
	}
</script>\n";

			echo "<input type='hidden' name='periode_num' id='periode_num' value='$periode_num' />\n";
			echo "Période $periode_num&nbsp;:";
			if((isset($id_grp_prec))&&($id_grp_prec!=0)) {
				//onclick=\"return confirm_abandon (this, 'yes', '$themessage')\" 
				//arrow-left.png
				echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num' title='Groupe précédent'><img src='../images/icons/back.png' class='icone16' alt='Groupe précédent' /></a>\n";
			}
			echo "<label for='id_groupe' class='invisible' >Changer de groupe</label>\n";
			echo "<select name='id_groupe' id='id_groupe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
			echo $chaine_options_classes;
			echo "</select>\n";
			if((isset($id_grp_suiv))&&($id_grp_suiv!=0)) {
				//onclick=\"return confirm_abandon (this, 'yes', '$themessage')\" 
				//arrow-right.png
				echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num' title='Groupe suivant'><img src='../images/icons/forward.png' class='icone16' alt='Groupe suivant' /></a>\n";
			}
			echo " | \n";
		}
	}
	// =================================
}

//if(isset($current_group)) { echo "DEBUG 3 : ".$current_group['classlist_string']."<br />";}

    echo "<a href='index.php?id_groupe=" . $current_group["id"] . "'> Choisir une autre période</a> | \n";

	// Recuperer la liste des cahiers de notes
	$sql="SELECT * FROM cn_cahier_notes ccn where id_groupe='$id_groupe' ORDER BY periode;";
	$res_cn=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_cn)>1) {
		// On ne propose pas de champ SELECT pour un seul canier de notes
		echo "<script type='text/javascript'>
var tab_per_cn=new Array();\n";

		$max_per=0;
		$chaine_options_periodes="";
		while($lig_cn=mysqli_fetch_object($res_cn)) {
			$chaine_options_periodes.="<option value='$lig_cn->id_cahier_notes'";
			if($lig_cn->periode==$periode_num) {$chaine_options_periodes.=" selected='selected'";}
			$chaine_options_periodes.=">$lig_cn->periode</option>\n";

			echo "tab_per_cn[$lig_cn->id_cahier_notes]=$lig_cn->periode;\n";

			if($lig_cn->periode>$max_per) {$max_per=$lig_cn->periode;}
		}

		$index_num_periode=$periode_num-1;

		echo "
	change='no';

	function confirm_changement_periode(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			//alert(document.getElementById('id_racine').selectedIndex);
			//alert(document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value);
			//alert(document.form1.elements['id_racine'].options[document.form1.elements['id_racine'].selectedIndex].value);
			//i=document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value;

			document.getElementById('periode_num').value=tab_per_cn[document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value];
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.getElementById('periode_num').value=tab_per_cn[document.getElementById('id_racine').options[document.getElementById('id_racine').selectedIndex].value];
				document.form1.submit();
			}
			else{
				document.getElementById('id_racine').selectedIndex=$index_num_periode;
			}
		}
	}
</script>\n";
	
		echo "<label for='id_racine' title='Accéder au cahier de notes de la période (ne sont proposées que les périodes pour lesquelles le cahier de notes a été initialisé)'>Période</label>&nbsp;:";
		if($periode_num>1) {
			$periode_prec=$periode_num-1;
			echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;periode_num=$periode_prec' title='Période précédente'><img src='../images/icons/back.png' class='icone16' alt='Période précédente' /></a>\n";
		}
		echo "<select name='id_racine' id='id_racine' onchange=\"confirm_changement_periode(change, '$themessage');\">\n";
		echo $chaine_options_periodes;
		echo "</select>";
		if($periode_num<$max_per) {
			$periode_suiv=$periode_num+1;
			echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;periode_num=$periode_suiv' title='Période suivante'><img src='../images/icons/forward.png' class='icone16' alt='Période suivante' /></a>\n";
		}
		echo " | \n";
	}

	/*
	// Ca ne fonctionne pas: On ne récupère que le dernier devoir consulté,... parce qu'imprime_pdf.php récupère ce qui est mis en $_SESSION['data_pdf']
	$sql="SELECT 1=1 FROM cn_devoirs cd, cn_conteneurs cc WHERE cd.id_conteneur=cc.id AND cc.id_racine='$id_racine';";
	$test_existence_devoir=mysql_query($sql);
	if(mysql_num_rows($test_existence_devoir)>0) {
		$titre_pdf = urlencode($current_group['description']." (".$nom_periode[$periode_num].")");
		echo "<a href=\"../fpdf/imprime_pdf.php?titre=$titre_pdf&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;nom_pdf_en_detail=oui\" title=\"Export PDF du Carnet de Notes\"> Imprimer au format PDF </a>|";
	}
	*/

	//if(isset($current_group)) { echo "DEBUG 4 : ".$current_group['classlist_string']."<br />";}

	//==================================
	// AJOUT: boireaus EXPORT...
    echo "<a href='export_cahier_notes.php?id_racine=".$id_racine."' title=\"Exporter les notes au format tableur\">Exporter les notes</a> | \n";
	//==================================

	if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
		echo "<a href='import_cahier_notes.php?id_racine=".$id_racine."' title=\"Importer les notes depuis un format tableur\">Importer les notes</a> | \n";
	}

    if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)||($acces_exceptionnel_saisie)) {
        if(getSettingAOui('GepiPeutCreerBoitesProf')) {
            echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_index'> Créer un";
            if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "e";}
            echo " ".htmlspecialchars(my_strtolower(getSettingValue("gepi_denom_boite")))." </a> | \n";
        }

        echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_index'> Créer une évaluation </a> | \n";
        if ($periode_num!='1')  {
            $themessage = 'En cliquant sur OK, vous allez créer la même structure de boîtes que celle de la période précédente. Si des boîtes existent déjà, elles ne seront pas supprimées.';
            if(getSettingAOui('GepiPeutCreerBoitesProf')) {
                echo "<a href='index.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;creer_structure=yes".add_token_in_url()."' onclick=\"return confirm_abandon (this, 'yes', '$themessage')\"> Créer la même structure que la période précédente</a> | \n";
            }
        }
    }

	// Le retour n'est pas parfait... il faudrait aussi periode_num dans chemin_retour
	// ou alors stocker ici l'info en session pour la période...

	if(acces_modif_liste_eleves_grp_groupes($id_groupe)) {
		echo "<a href='../groupes/grp_groupes_edit_eleves.php?id_groupe=$id_groupe' title=\"Si la liste des élèves du groupe affiché n'est pas correcte, vous êtes autorisé à modifier la liste.\">Modifier le groupe <img src='../images/icons/edit_user.png' class='icone16' alt=\"Modifier.\" /></a>";
	}
	else {
		echo "<a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/index.php?id_groupe=$id_groupe\" title=\"Si certains élèves sont affectés à tort dans cet enseignement, ou si il vous manque certains élèves, vous pouvez dans cette page signaler l'erreur à l'administrateur Gepi.\"> Signaler des erreurs d'affectation <img src='../images/icons/ico_attention.png' class='icone16' alt='Erreur' /></a>";
	}
	echo " | ";
	echo "<a href=\"index_cc.php?id_racine=$id_racine\"> ".ucfirst($nom_cc)."</a>";

	// 20201120
	if((getSettingAOui('export_vers_sacoche'))&&(isset($periode_num))) {
		// Tester si les identifiants SACoche sont renseignés
		$sql="SELECT DISTINCT e.id_sacoche FROM eleves e, 
						j_eleves_groupes jeg 
					WHERE e.login=jeg.login AND 
						jeg.id_groupe='".$id_groupe."' AND 
						jeg.periode='".$periode_num."' AND 
						e.id_sacoche!='0';";
		$res_sacoche=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_sacoche)>0) {
			echo " | ";
			echo "<a href=\"export_sacoche.php?id_groupe=$id_groupe&periode_num=$periode_num\"> Exporter les notes pour SACoche <span title=\"Les identifiants SACoche sont renseignés pour ".mysqli_num_rows($res_sacoche)." élèves sur ".count($current_group["eleves"][$periode_num]["list"]).".\">(".mysqli_num_rows($res_sacoche)."/".count($current_group["eleves"][$periode_num]["list"]).")</span></a>";
		}
	}

	echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";

	//if(isset($current_group)) { echo "DEBUG 5 : ".$current_group['classlist_string']."<br />";}

// 20180515
/*
echo "<div style='width:30em;float:left;'>";
echo "<pre>";
print_r($current_group);
echo "</pre>";
echo "</div>";
*/
	//if((isset($current_group["classes"]["list"]))&&(count($current_group["classes"]["list"])==1)) {
	if(isset($current_group["classes"]["list"])) {
		echo "<div style='float:right; width:30em; font-size:x-small;'>";
		foreach($current_group["classes"]["classes"] as $current_id_classe => $current_classe) {
			echo "<p class='bold'>".$current_classe['classe']."</p>".affiche_tableau_resp_classe($current_id_classe)."<br />";
		}
		echo "</div>";
	}


    echo "<h2 class='gepi'>Carnet de notes&nbsp;: ". htmlspecialchars($current_group["description"]) . " (<em>".$nom_periode[$periode_num];
    if(getSettingAOui('cn_affiche_date_fin_periode')) {
        echo " <span title='Fin de période' style='font-size:xx-small'>-&gt; ".formate_date($date_fin_periode[$periode_num])."</span>";
    }
    echo "</em>)";

	// 20210302
	$is_groupe_exclu_module_cn=is_groupe_exclu_tel_module($current_group['id'], 'cahier_notes');
	if($is_groupe_exclu_module_cn) {
		echo " <img src='../images/icons/ico_attention.png' class='icone16' title='Le carnet de notes est désactivé pour au moins une des classes associées à cet enseignement. Les notes saisies ne sont pas visibles des élèves et parents.' />";
		$acces_cn_prof_url_cn_officiel=getSettingValue('acces_cn_prof_url_cn_officiel');
		if($acces_cn_prof_url_cn_officiel!='') {
			echo "<a href='".$acces_cn_prof_url_cn_officiel."' target='_blank' title=\"Accéder à l'application officielle de saisie des résultats aux évaluations : $acces_cn_prof_url_cn_officiel.\"><img src='../images/lien.png' class='icone16' /></a>";
		}
	}

    echo "</h2>\n";
// 20160225
    echo "<p class='bold'> Classe(s) : " . $current_group["classlist_string"] . " | Matière : " . htmlspecialchars($current_group["matiere"]["nom_complet"]) . "(" . htmlspecialchars($current_group["matiere"]["matiere"]) . ")";
    // On teste si le carnet de notes est partagé ou non avec d'autres utilisateurs
    $login_prof = $_SESSION['login'];
    if (count($current_group["profs"]["list"]) > 1) {
        echo " | Carnet de notes partagé avec : ";
        $flag = 0;
        foreach($current_group["profs"]["users"] as $prof) {
            $l_prof = $prof["login"];
            $nom_prof = $prof["nom"];
            $prenom_prof = $prof["prenom"];
            if ($l_prof != $login_prof) {
                if ($flag > 0) echo ", ";
                echo $prenom_prof." ".$nom_prof;
                $flag++;
            }
        }
    }
    echo "</p>\n";

// 20180515
/*
echo "<div style='width:30em;float:left;'>";
echo "<pre>";
print_r($current_group);
echo "</pre>";
echo "</div>";
*/
//=========================
$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_groupe($id_groupe, $current_group, "left");
echo $chaine_date_conseil_classe;
//=========================

	if((isset($current_group["visibilite"]["cahier_notes"]))&&($current_group["visibilite"]["cahier_notes"]!='y')) {
		echo "<p style='color:red; text-indent:-7em;margin-left:7em;'><strong>ANOMALIE&nbsp;:</strong> Vous ne devriez pas saisir de notes dans ce carnet de notes.<br />L'enseignement courant est marqué comme ne devant pas avoir de carnet de notes.<br />Si vous y saisissez des notes, elles seront inexploitables.</p>";
	}

	if((isset($tab_anomalie_cn_pour_groupe_hors_cn))&&(count($tab_anomalie_cn_pour_groupe_hors_cn)>0)) {
		$info_anomalie="";
		foreach($tab_anomalie_cn_pour_groupe_hors_cn as $tmp_id_groupe => $tmp_cn) {
			$sql="SELECT * FROM cn_devoirs WHERE id_racine='$tmp_cn';";
			$res_cn_dev=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_cn_dev)>0) {
				if($info_anomalie=="") {
					$info_anomalie="<div style='border:1px solid red; margin: 1em;'><p><span style='color:red; font-weight:bold;'>ANOMALIE&nbsp;:</span> Un devoir au moins a été créé dans un enseignement qui ne doit normalement pas apparaître dans les Carnets de notes.<br />Il conviendrait de le supprimer, ou de le transférer (<em>si par exemple, il a été créé dans un sous-groupe, au lieu du groupe classe</em>).</p>\n";
				}

				$tmp_group=get_group($tmp_id_groupe);
				$info_anomalie.="<p class='bold'>Devoir(s) en ".$tmp_group['name']." (<em>".$tmp_group['description']."</em>) en ".$tmp_group['classlist_string']."&nbsp;:</p>\n";
				$info_anomalie.="<ul>\n";
				while($lig_dev=mysqli_fetch_object($res_cn_dev)) {
					$info_anomalie.="<li>";
					$info_anomalie.="<b>".$lig_dev->nom_court."</b>\n";
					if($lig_dev->nom_complet!='') {$info_anomalie.=" (<em>".$lig_dev->nom_complet."</em>)";}

					$sql="SELECT DISTINCT login FROM cn_notes_devoirs WHERE id_devoir='$lig_dev->id';";
					$test_notes_dev=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_notes_dev)==0) {
						$info_anomalie.=" - Aucune note - ";
					}
					else {
						$info_anomalie.=" - <a href='export_cahier_notes.php?id_racine=".$tmp_cn."' target='_blank'>Exporter les notes</a> - ";
					}

					$info_anomalie.="<a href='".$_SERVER['PHP_SELF']."?clean_anomalie_cn=y&amp;&amp;id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;suppr_id_dev=".$lig_dev->id.add_token_in_url()."'><img src='../images/delete16.png' class='icone16' alt='Supprimer ce devoir' title='Supprimer ce devoir' /></a>\n";
					$info_anomalie.="</li>\n";
				}
				$info_anomalie.="</ul>\n";
			}
		}
		if($info_anomalie!="") {
			$info_anomalie.="<p>Si vous pensez qu'un de ces enseignements devrait apparaître dans les carnets de notes, prenez contact avec l'administration de votre établissement.</p>\n";
			$info_anomalie.="</div>\n";
		}

		echo $info_anomalie;
	}

// 20180515
/*
echo "<p>\$id_racine=$id_racine<br />
id_groupe=".$current_group["id"]."</p>";
*/

	echo "<h3 class='gepi'>Liste des évaluations du carnet de notes</h3>\n";
	$empty = affiche_devoirs_conteneurs($id_racine,$periode_num, $empty, $current_group["classe"]["ver_periode"]["all"][$periode_num]);
	//echo "</ul>\n";

	if ($empty == 'yes') {
		echo "<p><b>Actuellement, aucune évaluation.</b> Vous devez créer au moins une évaluation.</p>\n";
	}
	else {
		echo "
<script type='text/javascript'>
	function change_visibilite_dev(id_dev, visible) {
		new Ajax.Updater($('span_visibilite_'+id_dev),'".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&id_racine=$id_racine&id_dev='+id_dev+'&mode=change_visibilite_dev&visible='+visible+'&mode_js=y&".add_token_in_url(false)."',{method: 'get'});
	}
</script>";
	}

	if($periode_num>=2) {
		echo "<p><a href='toutes_notes.php?id_groupe=$id_groupe'>Voir toutes les évaluations de l'année</a></p>\n";
	}

	if (($empty != 'yes')&&(getSettingAOui('active_bulletins'))) {

		// 20201120
		$tab_id_classe_exclues_module_bulletins=get_classes_exclues_tel_module('bulletins');
		$pas_de_bulletin=true;
		foreach($current_group['classes']['list'] as $tmp_id_classe) {
			if(!in_array($tmp_id_classe, $tab_id_classe_exclues_module_bulletins)) {
				$pas_de_bulletin=false;
				break;
			}
		}

		if(!$pas_de_bulletin) {

			$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='$id_groupe' AND domaine='bulletins' AND visible='n';";
			$test_jgv=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_jgv)==0) {
				//if (($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)||($acces_exceptionnel_saisie)) {
				if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
					echo "<h3 class='gepi'>Saisie du bulletin ($nom_periode[$periode_num])</h3>\n";
		
					$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND periode='$periode_num';";
					$res_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_ele_grp=mysqli_num_rows($res_ele_grp);
		
					$sql="SELECT 1=1 FROM matieres_notes WHERE id_groupe='$id_groupe' AND periode='$periode_num' AND statut!='-';";
					$res_mn=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_mn=mysqli_num_rows($res_mn);
					if($nb_mn==0) {
						$info_mn="<span style='color:red; font-size: small;'>(actuellement vide)</span>";
					}
					else {
						if($nb_mn==$nb_ele_grp) {
							$info_mn="<span style='color:green; font-size: small;'>($nb_mn/$nb_ele_grp)</span>";
						}
						else {
							$info_mn="<span style='color:red; font-size: small;'>($nb_mn/$nb_ele_grp)</span>";
						}
					}
		
					$sql="SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='$id_groupe' AND periode='$periode_num' AND appreciation!='';";
					$res_ma=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_ma=mysqli_num_rows($res_ma);
					if($nb_ma==0) {
						$info_ma="<span style='color:red; font-size: small;'>(actuellement vide)</span>";
					}
					else {
						if($nb_ma==$nb_ele_grp) {
							$info_ma="<span style='color:green; font-size: small;'>($nb_ma/$nb_ele_grp)</span>";
						}
						else {
							$info_ma="<span style='color:red; font-size: small;'>($nb_ma/$nb_ele_grp)</span>";
						}
					}

					echo "<ul><li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Saisie des moyennes</a> $info_mn</li>\n";
					echo "<li><a href='../saisie/saisie_appreciations.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num'>Saisie des appréciations</a> $info_ma</li></ul>\n";
				} else {
					echo "<h3 class='gepi'>Visualisation du bulletin ($nom_periode[$periode_num])</h3>\n";
					echo "<ul>\n";
					if(acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $periode_num)) {
						echo "<li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Accès exceptionnel à la correction des moyennes</a> (<b>".$gepiClosedPeriodLabel."</b>).</li>\n";
					}
					else {
						echo "<li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Visualisation des moyennes</a> (<b>".$gepiClosedPeriodLabel."</b>).</li>\n";
					}
					echo "<li><a href='../saisie/saisie_appreciations.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num'>Visualisation des appréciations</a> (<b>".$gepiClosedPeriodLabel."</b>).</li></ul>\n";
				}

				echo "
	<p style='margin-top:2em;'><em>NOTES&nbsp;:</em></p>
	<ul>
		<li>Lorsque la période est close, seule la consultation des notes saisies est possible.</li>
		<li>Lorsque la période est ouverte en saisie, vous pouvez créér/modifier des évaluations, des ".getSettingValue("gepi_denom_boite")."s,...</li>
		<li>
			En fin de période, il convient de provoquer une recopie des moyennes du carnet de notes vers le bulletin.<br />
			Cela permet de signaler à la personne éditant les bulletins que l'on a fini ses saisies.<br />
			Cela permet également de modifier les moyennes apparaissant.<br />
			Par exemple, vous pouvez décider de ne pas mettre de moyenne sur le bulletin pour un élève qui n'aurait été présent qu'à une évaluation<br />
			(<em>si vous estimez que la note n'est pas représentative du niveau de l'élève</em>).
		</li>
	</ul>";
			}
		}
	}

	if((isset($id_racine))&&(getPref($_SESSION['login'], 'cnBoitesModeMoy', '')=="")) {
		$sql="SELECT 1=1 FROM cn_conteneurs WHERE id_racine='$id_racine';";
		$res_nb_conteneurs=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_nb_conteneurs)>1) {
			echo "<p><br /></p><p><strong style='color:red'>ATTENTION&nbsp;:</strong> Vous n'avez pas encore choisi le mode de calcul de moyenne que vous souhaitez adopter <strong>par défaut</strong> quand vous créez des ".getSettingValue('gepi_denom_boite')."s.</p>\n";
			echo "<div style='margin-left:7em;'>";
			include("explication_moyenne_boites.php");
			echo "<p><br /></p>\n";
			echo "<p><a href='../utilisateurs/mon_compte.php#cnBoitesModeMoy' target='_blank'>Choisir le mode par défaut pour mes ".getSettingValue('gepi_denom_boite')."s</a>.<br />Cela ne vous empêchera pas de choisir un autre mode pour des ".getSettingValue('gepi_denom_boite')."s particulier(e)s.<br />Cela ne modifie pas non plus le mode de calcul dans les carnets de notes existants.</p>\n";
			echo "<p><br /></p>\n";
			if((isset($id_racine))&&(getSettingAOui('GepiPeutCreerBoitesProf'))) {
				echo "<p><a href='add_modif_conteneur.php?id_conteneur=$id_racine&mode_navig=retour_index' target='_blank'>Paramétrer le mode de calcul pour les ".getSettingValue('gepi_denom_boite')."s</a> de ce carnet de notes (<em>". htmlspecialchars($current_group["description"])." (".$nom_periode[$periode_num].")</em>) en particulier.</p>\n";
			}
			echo "</div>";
		}
	}
}

if (isset($_GET['id_groupe']) and !(isset($_GET['periode_num'])) and !(isset($id_racine))) {

	$matiere_nom = $current_group["matiere"]["nom_complet"];
	$matiere_nom_court = $current_group["matiere"]["matiere"];

	$nom_classes = $current_group["classlist_string"];

	$is_groupe_exclu_module_cn=is_groupe_exclu_tel_module($_GET['id_groupe'], 'cahier_notes');

	echo "<p class='bold'>";
	echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
	echo "<a href='index.php?id_groupe=no_group'> Mes enseignements </a></p>\n";
	echo "<p class='bold'>Enseignement&nbsp;: ".htmlspecialchars($current_group["description"])." (" . $current_group["classlist_string"] .")";
	if($is_groupe_exclu_module_cn) {
		echo " <img src='../images/icons/ico_attention.png' class='icone16' title='Le carnet de notes est désactivé pour au moins une des classes associées à cet enseignement. Les notes saisies ne sont pas visibles des élèves et parents.' />";
		$acces_cn_prof_url_cn_officiel=getSettingValue('acces_cn_prof_url_cn_officiel');
		if($acces_cn_prof_url_cn_officiel!='') {
			echo "<a href='".$acces_cn_prof_url_cn_officiel."' target='_blank' title=\"Accéder à l'application officielle de saisie des résultats aux évaluations : $acces_cn_prof_url_cn_officiel.\"><img src='../images/lien.png' class='icone16' /></a>";
		}
	}
	echo "</p>\n";

	echo "<h3>Visualisation/modification - Choisissez la période : </h3>
	<div style='margin-left:3em;'>\n";
	$i="1";
	while ($i < ($current_group["nb_periode"])) {
		echo "<p><a href='index.php?id_groupe=$id_groupe&amp;periode_num=$i'>".ucfirst($current_group["periodes"][$i]["nom_periode"])."</a>";

		$sql="SELECT * FROM periodes WHERE num_periode='$i' AND id_classe='".$current_group["classes"]["list"][0]."' AND verouiller='N'";
		$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_test)==0){
			echo " (<i>période close</i>)";

			if(acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $i)) {
				echo " (<em>Accès exceptionnellement ouvert en saisie</em>)";
			}

		}

		echo "</p>\n";
		$i++;
	}
	echo "
</div>

<h3>Visualisation uniquement : </h3>
<div style='margin-left:3em;'>
	<p><a href='toutes_notes.php?id_groupe=$id_groupe'>Voir toutes les évaluations de l'année</a></p>
</div>

<p style='margin-top:2em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Lorsqu'une période est close, seule la consultation des notes saisies est possible.</li>
	<li>Lorsqu'une période est ouverte en saisie, vous pouvez créér/modifier des évaluations, des ".getSettingValue("gepi_denom_boite")."s,...</li>
</ul>\n";
}

if (!(isset($_GET['id_groupe'])) and !(isset($_GET['periode_num'])) and !(isset($id_racine))) {
	?>
	<p class='bold'><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>
	<p>Accéder au carnet de notes : </p>
	<?php
	$groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");

	if (empty($groups)) {
		echo "<br /><br />";
		echo "<b>Aucun cahier de notes n'est disponible.</b>";
		echo "<br /><br />";
	}
	else {
		$nb_groupes_affiches=0;
		foreach($groups as $group) {
			// 20210301
			$is_groupe_exclu_module_cn=is_groupe_exclu_tel_module($group["id"], 'cahier_notes');
			if(((getSettingAOui('acces_cn_prof'))&&($pref_acces_cn_prof_afficher_lien=='y'))||
			(!$is_groupe_exclu_module_cn)) {
				if((!isset($group["visibilite"]["cahier_notes"]))||($group["visibilite"]["cahier_notes"]=='y')) {
					echo "<p><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
					echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . htmlspecialchars($group["description"]) . "</a>";
					echo "</span>";
					if($is_groupe_exclu_module_cn) {
						echo " <img src='../images/icons/ico_attention.png' class='icone16' title='Le carnet de notes est désactivé pour au moins une des classes associées à cet enseignement. Les notes saisies ne sont pas visibles des élèves et parents.' />";
						$acces_cn_prof_url_cn_officiel=getSettingValue('acces_cn_prof_url_cn_officiel');
						if($acces_cn_prof_url_cn_officiel!='') {
							echo "<a href='".$acces_cn_prof_url_cn_officiel."' target='_blank' title=\"Accéder à l'application officielle de saisie des résultats aux évaluations : $acces_cn_prof_url_cn_officiel.\"><img src='../images/lien.png' class='icone16' /></a>";
						}
					}
					echo "</p>\n";
					$nb_groupes_affiches++;
				}
			}
		}
		if($nb_groupes_affiches==0) {
			echo "<br /><br />";
			echo "<b>Aucun cahier de notes n'est disponible.</b>";
			echo "<br /><br />";
		}
	}
}
/*
$periode_num=1;
$login_ele=$current_group['eleves'][$periode_num]["list"][0];
$tab=get_tab_notes_ele($login_ele, $id_groupe, $periode_num);
echo "<pre>";
print_r($tab);
echo "</pre>";
echo "<hr />";
$tab=get_tab_notes($id_groupe, $periode_num);
echo "<pre>";
print_r($tab);
echo "</pre>";
*/

  /**
   * Pied de page
   */
require("../lib/footer.inc.php");
?>
