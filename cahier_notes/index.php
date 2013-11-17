<?php
/**
 * Arborescence des évaluations
 * 
 *
 * @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$msg="";

unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
if ($id_groupe == "no_group") {
    $id_groupe = NULL;
    unset($_GET['id_groupe']);
    $_SESSION['id_groupe_session'] = "";
}

//on met le groupe dans la session, pour naviguer entre absence, cahier de texte et autres
if ($id_groupe != NULL) {
    $_SESSION['id_groupe_session'] = $id_groupe;
} else if (isset($_SESSION['id_groupe_session']) && $_SESSION['id_groupe_session'] != "") {
     $_GET['id_groupe'] = $_SESSION['id_groupe_session'];
     $id_groupe = $_SESSION['id_groupe_session'];
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

	if(!empty($tab_groups)) {

		$chaine_options_classes="";

		$num_groupe=-1;
		$nb_groupes_suivies=count($tab_groups);
        
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if((!isset($tab_groups[$loop]["visibilite"]["cahier_notes"]))||($tab_groups[$loop]["visibilite"]["cahier_notes"]=='y')) {
				// On ne retient que les groupes qui ont un nombre de périodes au moins égal à la période sélectionnée
				if($tab_groups[$loop]["nb_periode"]>=$periode_num) {
					if($tab_groups[$loop]['id']==$id_groupe){
						$num_groupe=$loop;
	
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."' selected='selected'>".htmlspecialchars($tab_groups[$loop]['description'])." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
	
						$temoin_tmp=1;
						if(isset($tab_groups[$loop+1])){
							$id_grp_suiv=$tab_groups[$loop+1]['id'];
						}
						else{
							$id_grp_suiv=0;
						}
					}
					else {
						$chaine_options_classes.="<option value='".$tab_groups[$loop]['id']."'>".htmlspecialchars($tab_groups[$loop]['description'])." (".$tab_groups[$loop]['classlist_string'].")</option>\n";
					}
	
					if($temoin_tmp==0){
						$id_grp_prec=$tab_groups[$loop]['id'];
					}
				}
			}
			elseif(get_cn_from_id_groupe_periode_num($tab_groups[$loop]['id'], $periode_num)!="") {
				$tab_anomalie_cn_pour_groupe_hors_cn[$tab_groups[$loop]['id']]=get_cn_from_id_groupe_periode_num($tab_groups[$loop]['id'], $periode_num);
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
	echo "<a href=\"../groupes/signalement_eleves.php?id_groupe=$id_groupe&amp;chemin_retour=../cahier_notes/index.php?id_groupe=$id_groupe\" title=\"Si certains élèves sont affectés à tort dans cet enseignement, ou si il vous manque certains élèves, vous pouvez dans cette page signaler l'erreur à l'administrateur Gepi.\"> Signaler des erreurs d'affectation <img src='../images/icons/ico_attention.png' class='icone16' alt='Erreur' /></a>";

	echo " | ";
	echo "<a href=\"index_cc.php?id_racine=$id_racine\"> ".ucfirst($nom_cc)."</a>";

    echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";

	//if(isset($current_group)) { echo "DEBUG 5 : ".$current_group['classlist_string']."<br />";}

    echo "<h2 class='gepi'>Carnet de notes : ". htmlspecialchars($current_group["description"]) . " ($nom_periode[$periode_num])</h2>\n";
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


    echo "<h3 class='gepi'>Liste des évaluations du carnet de notes</h3>\n";
    $empty = affiche_devoirs_conteneurs($id_racine,$periode_num, $empty, $current_group["classe"]["ver_periode"]["all"][$periode_num]);
    //echo "</ul>\n";

    if ($empty == 'yes') echo "<p><b>Actuellement, aucune évaluation.</b> Vous devez créer au moins une évaluation.</p>\n";

	if($periode_num>=2) {
		echo "<p><a href='toutes_notes.php?id_groupe=$id_groupe'>Voir toutes les évaluations de l'année</a></p>\n";
	}

    if (($empty != 'yes')&&(getSettingAOui('active_bulletins'))) {
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

    echo "<p class='bold'>";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
    echo "<a href='index.php?id_groupe=no_group'> Mes enseignements </a></p>\n";
    echo "<p class='bold'>Enseignement : ".htmlspecialchars($current_group["description"])." (" . $current_group["classlist_string"] .")</p>\n";

    echo "<h3>Visualisation/modification - Choisissez la période : </h3>\n";
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
    echo "<h3>Visualisation uniquement : </h3>\n";
    echo "<p><a href='toutes_notes.php?id_groupe=$id_groupe'>Voir toutes les évaluations de l'année</a></p>\n";

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

    foreach($groups as $group) {
		if((!isset($group["visibilite"]["cahier_notes"]))||($group["visibilite"]["cahier_notes"]=='y')) {
			echo "<p><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
			echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . htmlspecialchars($group["description"]) . "</a>";
			echo "</span></p>\n";
		}
    }
}
  /**
   * Pied de page
   */
require("../lib/footer.inc.php");
?>
