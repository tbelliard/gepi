<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

function log_debug($texte) {
	$fich=fopen("/tmp/debug.txt","a+");
	fwrite($fich,$texte."\n");
	fclose($fich);
}

//log_debug('Avant initialisations');

// Initialisations files
require_once("../lib/initialisations.inc.php");

//log_debug('Après initialisations');

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

//log_debug('Après $session_gepi->security_check()');

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['retour_apres_maj_sconet'])) {
	unset($_SESSION['retour_apres_maj_sconet']);
}

//debug_var();

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
	if((isset($_GET['mode']))&&($_GET['mode']=='update_champs_periode')&&(isset($_GET['id_classe']))) {
		check_token();

		$sql="SELECT * FROM periodes WHERE id_classe='".$_GET['id_classe']."' ORDER BY num_periode;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			while($lig=mysql_fetch_object($res)) {
				echo "<input type='checkbox' id='num_periode_".$lig->num_periode."' name='num_periode[]' value='".$lig->num_periode."' /><label for='num_periode_".$lig->num_periode."'>".$lig->nom_periode."</label><br />";
			}
		}
		die();
	}

	if((isset($_GET['mode']))&&($_GET['mode']=='update_champs_choix_prof_suivi')&&(isset($_GET['login_ele']))) {
		check_token();

		// Afficher la liste des classes en opt group, puis la liste des profs de chaque classe en mettant en couleur ceux qui sont déjà PP d'autres élèves de la classe
		$sql="SELECT DISTINCT id_classe, classe FROM j_eleves_classes jec, classes c WHERE jec.login='".$_GET['login_ele']."' AND jec.id_classe=c.id ORDER BY periode;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<sapn style='color:red'>Cet élève n'est dans aucune classe&nbsp;???</span>";
		}
		else {
			echo "<select name='prof_suivi_choisi' id='prof_suivi_choisi'>";
			while($lig=mysql_fetch_object($res)) {
				echo "<optgroup label=\"Classe de $lig->classe\">";

				$tab_pp=array();
				$sql="SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$lig->id_classe';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					while($lig2=mysql_fetch_object($res2)) {
						$tab_pp[]=$lig2->professeur;
					}
				}

				$pp_actuel="";
				$sql="SELECT professeur FROM j_eleves_professeurs WHERE login='".$_GET['login_ele']."' AND id_classe='$lig->id_classe';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$pp_actuel=mysql_result($res2, 0, 'professeur');
				}

				$sql="SELECT DISTINCT u.login FROM j_groupes_classes jgc, j_groupes_professeurs jgp, utilisateurs u WHERE jgc.id_groupe=jgp.id_groupe AND jgc.id_classe='$lig->id_classe' AND u.login=jgp.login ORDER BY u.nom, u.prenom;";
				$res3=mysql_query($sql);
				if(mysql_num_rows($res3)>0) {
					while($lig3=mysql_fetch_object($res3)) {
						echo "<option value='$lig->id_classe|$lig3->login'";
						if(in_array($lig3->login, $tab_pp)) {
							echo " style='background-color:green;' title=\"Ce professeur est ".getSettingValue('gepi_prof_suivi')." d'un ou plusieurs autres élèves de la classe.\"";
						}
						if($lig3->login==$pp_actuel) {echo " selected";}
						echo ">".civ_nom_prenom($lig3->login)."</option>";
					}
				}
				echo "</optgroup>";
			}
			echo "</select>";
		}

		die();
	}

	if((isset($_GET['mode']))&&($_GET['mode']=='modif_prof_suivi')&&(isset($_GET['login_ele']))&&(isset($_GET['prof_suivi']))) {
		check_token();

		// On reçoit prof_suivi_choisi au format id_classe|login

		$tab=explode("|", $_GET['prof_suivi']);
		if(isset($tab[1])) {
			$sql="SELECT 1=1 FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='$tab[1]' AND jgc.id_classe='$tab[0]'";
			//echo "$sql<br />";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='".$_GET['login_ele']."' AND id_classe='$tab[0]'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)>0) {
					$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE login='".$_GET['login_ele']."' AND id_classe='$tab[0]'";
					$test=mysql_query($sql);
					if(mysql_num_rows($test)>0) {
						$sql="UPDATE j_eleves_professeurs SET professeur='$tab[1]' WHERE login='".$_GET['login_ele']."' AND id_classe='$tab[0]'";
					}
					else {
						$sql="INSERT INTO j_eleves_professeurs SET professeur='$tab[1]', login='".$_GET['login_ele']."', id_classe='$tab[0]'";
					}
					$res=mysql_query($sql);
					if($res) {
						echo civ_nom_prenom($tab[1]);
					}
					else {
						echo "<span style='color:red'>Erreur</span>";
					}
				}
			}
		}
		die();
	}
}

$mode_rech=isset($_POST['mode_rech']) ? $_POST['mode_rech'] : (isset($_GET['mode_rech']) ? $_GET['mode_rech'] : NULL);
if((isset($quelles_classes))&&(isset($mode_rech))&&($mode_rech=='contient')) {
	// On initialise des variables pour index_call_data.php
	if($quelles_classes=='recherche') {
		$mode_rech_nom="contient";
	}
	elseif($quelles_classes=='rech_prenom') {
		$mode_rech_prenom="contient";
	}
	elseif($quelles_classes=='rech_elenoet') {
		$mode_rech_elenoet="contient";
	}
	elseif($quelles_classes=='rech_ele_id') {
		$mode_rech_ele_id="contient";
	}
	elseif($quelles_classes=='rech_no_gep') {
		$mode_rech_no_gep="contient";
	}
}

//log_debug('Après checkAccess()');

//log_debug(debug_var());
//debug_var();

/*if(isset($_GET['csv'])) {
	check_token();

	// La solution en GET ne fonctionne pas bien au niveau de l'encodage/décodage et si suhosin est actif, c'est la longueur de la chaine $_GET qui pose pb.
	$nom_fic = "liste_eleve_gepi".strftime("%Y%m%d_%H%M%S").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo urldecode($_GET['csv']);
	die();
}
*/

 //répertoire des photos

// En multisite, on ajoute le répertoire RNE
if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	  // On récupère le RNE de l'établissement
	$rep_photos='../photos/'.$_COOKIE['RNE'].'/eleves/';

	//============================================
	// Pour le multisite
	if(!file_exists($rep_photos)) {
		//@mkdir($rep_photos);
		$tmp_tab=explode("/",$rep_photos);
		$chemin="";
		for($loop=0;$loop<count($tmp_tab);$loop++) {
			if($loop>0) {
				$chemin.="/";
			}
			$chemin.=$tmp_tab[$loop];
			if($tmp_tab[$loop]!='..') {
				@mkdir($chemin);
			}
		}
	}
	//============================================

}
else {
	$rep_photos='../photos/eleves/';
}

$tab_regimes_autorises=array('d/p', 'int.', 'ext.', 'i-e');
if(($_SESSION['statut']=='administrateur')&&(isset($_GET['initialiser_regimes']))&&(in_array($_GET['initialiser_regimes'],$tab_regimes_autorises))) {
	check_token();

	$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec
		LEFT JOIN j_eleves_regime jer ON jec.login=jer.login
		WHERE jer.login is null;";
	//echo "$sql<br />";
	$test_no_regime=mysql_query($sql);
	$test_no_regime_effectif=mysql_num_rows($test_no_regime);
	if($test_no_regime_effectif>0){
		$nb_reg_regime=0;
		$nb_err_regime=0;
		while($lig_reg=mysql_fetch_object($test_no_regime)) {
			$sql="INSERT INTO j_eleves_regime SET login='".$lig_reg->login."', regime='".$_GET['initialiser_regimes']."';";
			$insert=mysql_query($sql);
			if($insert) {$nb_reg_regime++;} else {$nb_err_regime++;}
		}

		$msg="";
		if($nb_reg_regime>0) {
			$msg.="$nb_reg_regime régime(s) ont été initialisés.<br />";
		}
		if($nb_err_regime>0) {
			$msg.="$nb_err_regime erreur(s) se sont produites lors de l'initialisation des régimes.<br />";
		}
	}
	else {
		$msg="Tous les régimes étaient déjà renseignés.<br />";
	}
}

$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
if($_SESSION['statut']=="professeur") {
	if(getSettingValue('GepiAccesGestElevesProfP')!='yes') {
		tentative_intrusion("2", "Tentative d'accès par un prof à des fiches élèves, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cette page car l'accès professeur n'est pas autorisé !";
		require ("../lib/footer.inc.php");
		die();
	}
	else {
		// Le professeur est-il professeur principal dans une classe au moins.
		$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."';";
		$test=mysql_query($sql);
		if (mysql_num_rows($test)==0) {
			tentative_intrusion("2", "Tentative d'accès par un prof qui n'est pas $gepi_prof_suivi à des fiches élèves, sans en avoir l'autorisation.");
			echo "Vous ne pouvez pas accéder à cette page car vous n'êtes pas $gepi_prof_suivi !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}

if (isset($is_posted) and ($is_posted == '2')) {
	//$tab_id_classe_quelles_classes=array();
	if ($quelles_classes == 'certaines') {
		//
		// On efface les enregistrements liés à la session en cours
		//
		mysql_query("DELETE FROM tempo WHERE num = '".SESSION_ID()."'");
		//
		// On efface les enregistrements obsolètes
		//
		$call_data = mysql_query("SELECT * FROM tempo");
		$nb_enr = mysql_num_rows($call_data);
		$nb = 0;
		while ($nb < $nb_enr) {
			$num = mysql_result($call_data, $nb, 'num');
			$test = mysql_query("SELECT * FROM log WHERE SESSION_ID = '$num'");
			$nb_en = mysql_num_rows($test);
			if ($nb_en == 0) {
				mysql_query("DELETE FROM tempo WHERE num = '$num'");
			}
			$nb++;
		}

		$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nb = mysql_num_rows($classes_list);
		$i ='0';
		while ($i < $nb) {
			$id_classe = mysql_result($classes_list, $i, 'id');
			$tempo = "case_".$id_classe;
			$temp = isset($_POST[$tempo])?$_POST[$tempo]:NULL;
			if ($temp == 'yes') {
				$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
				$nb_periode = mysql_num_rows($periode_query);
				$call_reg = mysql_query("insert into tempo Values('$id_classe','$nb_periode', '".SESSION_ID()."')");
				//$tab_id_classe_quelles_classes[]=$id_classe;
			}
			$i++;
		}
	}
}

// Le statut scolarite ne devrait pas être proposé ici.
// La page confirm_query.php n'est accessible qu'en administrateur
if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	if (isset($is_posted) and ($is_posted == '1')) {

		check_token();

		$delete_eleve=isset($_POST['delete_eleve']) ? $_POST['delete_eleve'] : array();
		if(!is_array($delete_eleve)) {$delete_eleve=array();$msg="Erreur: La liste d'élèves à supprimer devrait être un tableau.<br />";}

		$calldata = mysql_query("SELECT * FROM eleves");
		$nombreligne = mysql_num_rows($calldata);
		$i = 0;
		$liste_cible = '';
		$liste_cible2 = '';
		while ($i < $nombreligne){
			$eleve_login = mysql_result($calldata, $i, "login");
			$eleve_elenoet = mysql_result($calldata, $i, "elenoet");
			//$delete_login = 'delete_'.$eleve_login;
			//$del_eleve = isset($_POST[$delete_login])?$_POST[$delete_login]:NULL;
			//if ($del_eleve == 'yes') {
			if(in_array($eleve_login,$delete_eleve)) {
				$liste_cible = $liste_cible.$eleve_login.";";
				$liste_cible2 = $liste_cible2.$eleve_elenoet.";";
			}
			$i++;
		}
		//header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&amp;action=del_eleve");
		if($liste_cible!=''){
			header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&liste_cible2=$liste_cible2&action=del_eleve".add_token_in_url(false));
		}
	}
}

// pour l'envoi des photos du trombinoscope

if (empty($_POST['action']) and empty($_GET['action'])) { $action = ""; }
	else { if (empty($_POST['action'])){$action = ""; } if (empty($_GET['action'])){$action = $_POST['action'];} }
if (empty($_POST['total_photo']) and empty($_GET['total_photo'])) { $total_photo = ""; }
	else { if (empty($_POST['total_photo'])){$total_photo = ""; } if (empty($_GET['total_photo'])){$total_photo = $_POST['total_photo'];} }
if (empty($_FILES['photo'])) { $photo = ""; } else { $photo = $_FILES['photo']; }
if (empty($_POST['quiestce'])) { $quiestce = ""; } else { $quiestce = $_POST['quiestce']; }



function deplacer_fichier_upload($source, $dest) {
	$ok = @copy($source, $dest);
	if (!$ok) $ok = @move_uploaded_file($source, $dest);
	return $ok;
}


function test_ecriture_backup() {
	$ok = 'no';
	if ($f = @fopen($rep_photos."test", "w")) {
		@fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
		@fclose($f);
		include($rep_photos."test");
		$del = @unlink($rep_photos."test");
	}
	return $ok;
}

if (isset($action) and ($action == 'depot_photo') and $total_photo != 0)  {
	check_token();

	$msg="";
	$cpt_photos_mises_en_place=0;
	$cpt_photo = 0;
	while($cpt_photo < $total_photo)
	{

		//echo "\$quiestce[$cpt_photo]=".$quiestce[$cpt_photo]."<br />";
		if((isset($_FILES['photo']['type'][$cpt_photo]))&&($_FILES['photo']['type'][$cpt_photo] != ""))
		{
			unset($login_eleve);
			$acces_upload_photo="y";
			if(($_SESSION['statut']=='cpe')&&(!getSettingAOui('CpeAccesUploadPhotosEleves'))) {
				$acces_upload_photo="n";
			}
			elseif(($_SESSION['statut']=='professeur')&&(!getSettingAOui('GepiAccesGestPhotoElevesProfP'))) {
				$acces_upload_photo="n";
			}
			elseif($_SESSION['statut']=='professeur') {
				// Les PP ont accès à l'upload de photo de leurs élèves

				// Le prof est-il PP de cet élève ou de la classe de cet élève
				// Récupérer le login et la classe de l'élève
				$sql="SELECT login FROM eleves WHERE elenoet='".$quiestce[$cpt_photo]."';";
				$res_login=mysql_query($sql);
				if(mysql_num_rows($res_login)==0) {
					$msg.="Anomalie : Impossible de trouver le login de l'élève dont l'ELENOET est ".$quiestce[$cpt_photo]."<br />";
					$acces_upload_photo="n";
				}
				else {
					$login_eleve=mysql_result($res_login, 0, "login");

					if(!is_pp($_SESSION['login'], "", $login_eleve)) {
						// Le prof n'est pas PP de cet élève en particulier
						// A-t-il accès à tous les élèves de la classe dont-il est PP?
						if(!getSettingAOui('GepiAccesPPTousElevesDeLaClasse')) {
							$acces_upload_photo="n";
						}
						else {
							$acces_upload_photo="n";

							// On cherche alors la classe de l'élève
							$sql="SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$login_eleve' ORDER BY periode,classe;";
							$res_class=mysql_query($sql);
							if(mysql_num_rows($res_class)>0){
								while($lig_tmp=mysql_fetch_object($res_class)){
									if(is_pp($_SESSION['login'], $lig_tmp->id_classe)) {
										$acces_upload_photo="y";
										break;
									}
								}
							}
						}
					}
				}
			}

			if($acces_upload_photo!="y") {
				if(!isset($login_eleve)) {
					$sql="SELECT login FROM eleves WHERE elenoet='".$quiestce[$cpt_photo]."';";
					$res_login=mysql_query($sql);
					if(mysql_num_rows($res_login)==0) {
						$msg.="Anomalie : Impossible de trouver le login de l'élève dont l'ELENOET est ".$quiestce[$cpt_photo]."<br />";
					}
					else {
						$login_eleve=mysql_result($res_login, 0, "login");
						$msg.="Vous n'avez pas le droit d'uploader la photo pour ".civ_nom_prenom($login_eleve)."<br />";
					}
				}
				else {
					$msg.="Vous n'avez pas le droit d'uploader la photo pour ".civ_nom_prenom($login_eleve)."<br />";
				}
			}
			else {
				$sav_photo = isset($_FILES["photo"]) ? $_FILES["photo"] : NULL;
				if (!isset($sav_photo['tmp_name'][$cpt_photo]) or ($sav_photo['tmp_name'][$cpt_photo] =='')) {
					$msg.="Erreur de téléchargement niveau 1 (<i>photo n°$cpt_photo</i>).<br />";
				} else if (!file_exists($sav_photo['tmp_name'][$cpt_photo])) {
					$msg.="Erreur de téléchargement niveau 2 (<i>photo n°$cpt_photo</i>).<br />";
				} else if (my_strtolower($sav_photo['type'][$cpt_photo])!="image/jpeg") {
					$msg.="Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés (<i>".$sav_photo['name'][$cpt_photo]."&nbsp;: ".$sav_photo['type'][$cpt_photo]."</i>)<br />";
				} else if (!(preg_match('/jpg$/i',$sav_photo['name'][$cpt_photo]) || preg_match('/jpeg$/i',$sav_photo['name'][$cpt_photo]))) {
					$msg.="Erreur : seuls les fichiers ayant l'extension .jpg ou .jpeg sont autorisés (<i>".$sav_photo['name'][$cpt_photo]."</i>)<br />";
				} else {
					$dest = $rep_photos;
					$n = 0;
					//$msg.="\$rep_photos=$rep_photos<br />";
					if (!deplacer_fichier_upload($sav_photo['tmp_name'][$cpt_photo], $rep_photos.encode_nom_photo($quiestce[$cpt_photo]).".jpg")) {
						$msg.="Problème de transfert : le fichier n°$cpt_photo n'a pas pu être transféré sur le répertoire photos/eleves/<br />";
					} else {
						//$msg = "Téléchargement réussi.";
						$cpt_photos_mises_en_place++;
						if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
							// si le redimensionnement des photos est activé on redimensionne
								if (getSettingValue("active_module_trombinoscopes_rt")!='')
									$redim_OK=redim_photo($rep_photos.encode_nom_photo($quiestce[$cpt_photo]).".jpg",getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"),getSettingValue("active_module_trombinoscopes_rt"));
								else
									$redim_OK=redim_photo($rep_photos.encode_nom_photo($quiestce[$cpt_photo]).".jpg",getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
							if (!$redim_OK) $msg .= " Echec du redimensionnement de la photo.";
						}
					}
				}
			}
		}
		$cpt_photo = $cpt_photo + 1;
	}
	if(($msg=="")&&($cpt_photos_mises_en_place>0)) {$msg = "Téléchargement réussi.";}
}
// fin de l'envoi des photos du trombinoscope

//**************** EN-TETE *****************
$titre_page = "Gestion des élèves";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE *****************

if(getSettingValue('eleves_index_debug_var')=='y') {
	debug_var();
}
?>

<script type='text/javascript' language="JavaScript">
	function verif1() {
	<?php
		// Test d'existence de PMV... utilisé ici pour le file_exists() des photos
		/*
		if(getSettingValue("gepi_pmv")!="n"){
			echo "document.formulaire.quelles_classes[8].checked = true;";
		}
		else{
			echo "document.formulaire.quelles_classes[7].checked = true;";
		}
		*/
		echo "document.getElementById('quelles_classes_certaines').checked = true;";
	?>
	}
	function verif2() {
	<?php
		$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nb = mysql_num_rows($classes_list);
		$k = '0';
		while ($k < $nb) {
			$id_classe = mysql_result($classes_list, $k, 'id');

			?>
				document.formulaire.case_<?php echo $id_classe; ?>.checked = false;
				change_style_classe(<?php echo $id_classe; ?>);
			<?php
		$k++;
		}
	?>
	}

	function verif3(){
		document.getElementById('quelles_classes_recherche').checked=true;
		verif2();
	}

	function verif4(){
		document.getElementById('quelles_classes_rech_prenom').checked=true;
		verif2();
	}

	/*
	function verif5(){
		document.getElementById('quelles_classes_rech_champ').checked=true;
		verif2();
	}
	*/
	function verif5(){
		document.getElementById('quelles_classes_rech_elenoet').checked=true;
		verif2();
	}
	function verif6(){
		document.getElementById('quelles_classes_rech_ele_id').checked=true;
		verif2();
	}
	function verif7(){
		document.getElementById('quelles_classes_rech_no_gep').checked=true;
		verif2();
	}
</script>

<?php
if ($_SESSION['statut'] == 'administrateur') {
	$retour = "../accueil_admin.php";
}
else{
	$retour = "../accueil.php";
}
if (isset($quelles_classes)) {
	$retour = "index.php";
}
echo "<p class=bold><a href=\"".$retour."\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>\n";



if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";

		if($_SESSION['statut']=="administrateur"){
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		}
		else{
			echo "<p><a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contactez l'administrateur</a></p>\n";
		}

		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";

		if($_SESSION['statut']=="administrateur"){
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		}
		else{
			echo "<p><a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contactez l'administrateur</a></p>\n";
		}

		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";

		if($_SESSION['statut']=="administrateur"){
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		}
		else{
			echo "<p><a href=\"javascript:centrerpopup('../gestion/contacter_admin.php',600,480,'scrollbars=yes,statusbar=no,resizable=yes')\">Contactez l'administrateur</a></p>\n";
		}

			require("../lib/footer.inc.php");
			die();
		}
	}
}

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	echo " | <a href='add_eleve.php?mode=unique'>Ajouter un élève à la base (simple)</a>\n";
	echo " | <a href='add_eleve.php?mode=multiple'>Ajouter des élèves à la base (à la chaîne)</a>\n";

	$droits = @sql_query1("SELECT ".$_SESSION['statut']." FROM droits WHERE id='/eleves/import_eleves_csv.php'");
	if ($droits == "V") {
		echo " | <a href=\"import_eleves_csv.php\" title=\"Télécharger le fichier des noms, prénoms, identifiants GEPI et classes\">Télécharger le fichier des élèves au format csv.</a>\n";
	}


	if((getSettingValue("import_maj_xml_sconet")==1)&&
		(
			($_SESSION['statut']=='administrateur')||
			(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiAccesMajSconetScol')))
		)
	) {
		echo " | <a href=\"../responsables/maj_import.php\">Mettre à jour depuis Sconet</a>\n";
	}

	if((getSettingValue("import_maj_xml_sconet")==1)&&($_SESSION['statut']=='administrateur')) {
		echo " | <a href=\"import_communes.php\">Importer les communes de naissance des élèves</a>\n";
	}


}

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {echo " | <a href='synchro_mail.php'>Synchroniser les adresses mail élèves</a>\n";}

if(($_SESSION['statut']=="administrateur")&&(getSettingValue('exp_imp_chgt_etab')=='yes')) {
	// Pour activer le dispositif:
	// DELETE FROM setting WHERE name='exp_imp_chgt_etab';INSERT INTO setting SET name='exp_imp_chgt_etab', value='yes';
	//echo " | ";
	echo "<br />";
	echo "Changement d'établissement: <a href='export_bull_eleve.php'>Export des bulletins</a>\n";
	echo " et <a href='import_bull_eleve.php'>Import des bulletins</a>\n";
}
echo "</p>\n";

echo "<center><p class='grand'>Visualiser \ modifier une fiche élève</p></center>\n";

$req = mysql_query("SELECT login FROM eleves");
$test = mysql_num_rows($req);
if ($test == '0') {
	echo "<p class='grand'>Attention : il n'y a aucun élève dans la base GEPI !</p>\n";
	if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
		echo "<p>Vous pouvez ajouter des élèves à la base en cliquant sur l'un des liens ci-dessus";
		if($_SESSION['statut']=="administrateur") {
			echo ", ou bien directement <br /><a href='../initialisation/index.php'>importer les élèves et les classes à partir de fichiers GEP</a></p>\n";
		}
		require("../lib/footer.inc.php");
		die();
	}
}

if (!isset($quelles_classes)) {

	if($_SESSION['statut'] == 'professeur') {
		$sql="SELECT DISTINCT c.* FROM j_eleves_professeurs jep, classes c WHERE c.id=jep.id_classe AND jep.professeur='".$_SESSION['login']."' ORDER BY c.classe;";
		$call_classes=mysql_query($sql);

		$nb_classes=mysql_num_rows($call_classes);
		if($nb_classes==0){
			echo "<p>Vous n'êtes pas $gepi_prof_suivi</p>\n";
			// AJOUTER UN RENSEIGNEMENT test_intrusion... (normalement c'est fait plus haut)
			require("../lib/footer.inc.php");
			die();
		}
		elseif($nb_classes==1){
			$lig_clas=mysql_fetch_object($call_classes);
			$quelles_classes=$lig_clas->id;
		}
		else{
			// Choix de la classe...

			// Affichage sur 3 colonnes
			$nb_classes_par_colonne=round($nb_classes/2);

			echo "<table width='100%' summary='Choix des classes'>\n";
			echo "<tr valign='top' align='center'>\n";

			$cpt_i = 0;

			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td align='left'>\n";

			while($lig_clas=mysql_fetch_object($call_classes)) {

				//affichage 2 colonnes
				if(($cpt_i>0)&&(round($cpt_i/$nb_classes_par_colonne)==$cpt_i/$nb_classes_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				echo "<a href='".$_SERVER['PHP_SELF']."?quelles_classes=$lig_clas->id'>$lig_clas->classe</a>";
				echo "<br />\n";
				$cpt_i++;
			}

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

		}
	}
	else {


		echo "<form enctype='multipart/form-data' action='index.php' method='post' name='formulaire'>\n";
		echo "<table cellpadding='5' width='100%' border='0' summary='Choix du mode'>\n";

		//echo add_token_field();

		// =====================================================

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_recherche' value='recherche' onclick='verif2()' checked />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont le nom \n";
		echo "<select name='mode_rech_nom' onchange='verif3()'>
		<option value='commence_par'>commence par</option>
		<option value='contient'".(((isset($mode_rech))&&($mode_rech=='contient'))?" selected":"").">contient</option>
		</select>";
		echo "<input type='text' name='motif_rech' id='motif_rech_nom' value='' onclick='verif3()' size='5' />\n";
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_rech_prenom' value='rech_prenom' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont le prénom \n";
		echo "<select name='mode_rech_prenom' onchange='verif4()'>
		<option value='commence_par'>commence par</option>
		<option value='contient'".(((isset($mode_rech))&&($mode_rech=='contient'))?" selected":"").">contient</option>
		</select>";
		echo "<input type='text' name='motif_rech_p' value='' onclick='verif4()' size='5' />\n";
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		/*
		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_rech_champ' value='rech_champ' onclick='verif2()' checked />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont l'\n";
		echo "<select name='champ_rech' onchange='verif5()'>
		<option value='elenoet'>identifiant Sconet (elenoet)</option>
		<option value='ele_id'>identifiant Sconet (ele_id)</option>
		<option value='no_gep'>identifiant national</option>
		</select>";
		echo "<select name='mode_rech_champ' onchange='verif5()'>
		<option value='commence_par'>commence par</option>
		<option value='contient'>contient</option>
		</select>";
		echo "<input type='text' name='motif_rech' id='motif_rech_champ' value='' onclick='verif5()' size='5' />\n";
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";
		*/

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_rech_elenoet' value='rech_elenoet' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont l'identifiant Sconet (<em>elenoet</em>) ";
		echo "<select name='mode_rech_elenoet' onchange='verif5()'>
		<option value='commence_par'>commence par</option>
		<option value='contient'".(((isset($mode_rech))&&($mode_rech=='contient'))?" selected":"").">contient</option>
		</select>";
		echo "<input type='text' name='motif_rech_elenoet' id='motif_rech_elenoet' value='' onclick='verif5()' size='5' />\n";
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_rech_ele_id' value='rech_ele_id' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont l'identifiant Sconet (<em>ele_id</em>) ";
		echo "<select name='mode_rech_ele_id' onchange='verif6()'>
		<option value='commence_par'>commence par</option>
		<option value='contient'".(((isset($mode_rech))&&($mode_rech=='contient'))?" selected":"").">contient</option>
		</select>";
		echo "<input type='text' name='motif_rech_ele_id' id='motif_rech_ele_id' value='' onclick='verif6()' size='5' />\n";
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_rech_no_gep' value='rech_no_gep' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont l'identifiant national ";
		echo "<select name='mode_rech_no_gep' onchange='verif7()'>
		<option value='commence_par'>commence par</option>
		<option value='contient'".(((isset($mode_rech))&&($mode_rech=='contient'))?" selected":"").">contient</option>
		</select>";
		echo "<input type='text' name='motif_rech_no_gep' id='motif_rech_no_gep' value='' onclick='verif7()' size='5' />\n";
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// 20130607
		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_rech_mef' value='rech_mef' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		//echo "<label for='' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Elève dont le MEF est ";
		echo "<select name='motif_rech_mef' onchange='verif7()'>
		<option value='' title=\"Par non référencée, il est entendu que le code MEF n'est pas dans la liste des MEF identifiés.
Mettre à jour votre table mef peut être une solution.\">Vide ou non référencée</option>";
		$sql="SELECT * FROM mef ORDER BY libelle_court, libelle_edition, libelle_long;";
		$res_mef=mysql_query($sql);
		if(mysql_num_rows($res_mef)>0) {
			while($lig_mef=mysql_fetch_object($res_mef)) {
				echo "
		<option value='$lig_mef->mef_code'>";
				if($lig_mef->libelle_edition!="") {
					echo $lig_mef->libelle_edition;
				}
				elseif($lig_mef->libelle_long!="") {
					echo $lig_mef->libelle_long;
				}
				elseif($lig_mef->libelle_court!="") {
					echo $lig_mef->libelle_court;
				}
				else {
					echo $lig_mef->mef_code;
				}
				echo "</option>";
			}
		}
		echo "
		</select>";
		if(acces('/mef/admin_mef.php', $_SESSION['statut'])) {echo " - <a href='../mef/admin_mef.php'>Gérer les MEF</a>";}
		echo "</span><br />\n";
		//echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		// =====================================================

		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_classes c ON c.login=e.login
			where c.login is NULL;";
		$test_na=mysql_query($sql);
		//if($test_na){
		if(mysql_num_rows($test_na)==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='na' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves sont affectés dans une classe.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_na' value='na' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_na' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves non affectés à une classe (<i>".mysql_num_rows($test_na)."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

// Eric Les élèves dont la date de sortie de l'établissement est renseignée      
		$sql="SELECT 1=1 FROM eleves e where e.date_sortie<>0";
		$test_dse=mysql_query($sql);
		if(mysql_num_rows($test_dse)==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='dse' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Aucun élève n'a une date de sortie de l'établissement renseignée.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_dse' value='dse' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_dse' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves dont la date de sortie de l'établissement est renseignée (<i>".mysql_num_rows($test_dse)."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";

			$sql="SELECT DISTINCT e.login FROM eleves e, j_eleves_classes jec where e.login=jec.login AND e.date_sortie<>0";
			$test_dse2=mysql_query($sql);
			if(mysql_num_rows($test_dse2)>0){
				echo "<tr>\n";
				echo "<td style='vertical-align:top;'>\n";
				echo "<input type='radio' name='quelles_classes' id='quelles_classes_dse_anomalie' value='dse_anomalie' onclick='verif2()' />\n";
				echo "</td>\n";
				echo "<td>\n";
				echo "<label for='quelles_classes_dse_anomalie' style='cursor: pointer;'>\n";
				echo "<span class='norme'>Les élèves dont la date de sortie de l'établissement est renseignée et qui sont pourtant inscrits dans une classe (<i>".mysql_num_rows($test_dse2)."</i>).</span><br />\n";
				echo "Les élèves partis en cours d'année risquent d'apparaître ici.<br />";
				echo "</label>\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
		}

		$sql="SELECT 1=1 FROM eleves WHERE elenoet='' OR no_gep='';";
		$test_incomplet=mysql_query($sql);
		if(mysql_num_rows($test_incomplet)==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='incomplet' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves ont leur Elenoet et leur Numéro national (INE) renseigné.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_incomplet' value='incomplet' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_incomplet' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves dont l'Elenoet ou le Numéro national (INE) n'est pas renseigné (<i>".mysql_num_rows($test_incomplet)."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		// =====================================================
		// Les photos
		if (getSettingValue("active_module_trombinoscopes")=='y') {
			$sql="SELECT elenoet FROM eleves WHERE elenoet!='';";
			$test_elenoet_ok=mysql_query($sql);
			if(mysql_num_rows($test_elenoet_ok)!=0){
				$cpt_photo_manquante=0;
				while($lig_tmp=mysql_fetch_object($test_elenoet_ok)){
					$test_photo=nom_photo($lig_tmp->elenoet);
					if($test_photo==""){
						$cpt_photo_manquante++;
					}
				}
				if($cpt_photo_manquante>0){
					echo "<tr>\n";
					echo "<td>\n";
					echo "<input type='radio' name='quelles_classes' id='quelles_classes_photo' value='photo' onclick='verif2()' />\n";
					echo "</td>\n";
					echo "<td>\n";
					echo "<label for='quelles_classes_photo' style='cursor: pointer;'>\n";
					echo "<span class='norme'>Parmi les élèves dont l'Elenoet est renseigné, $cpt_photo_manquante n'ont pas leur photo.</span><br />\n";
					echo "</label>\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
				else{
					echo "<tr>\n";
					echo "<td>\n";
					echo "&nbsp;\n";
					echo "</td>\n";
					echo "<td>\n";

					echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='photo' onclick='verif2()' /></span>\n";

					echo "<span class='norme'>Tous les élèves, dont l'Elenoet est renseigné, ont leur photo.</span><br />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
			}
			else{
				echo "<tr>\n";
				echo "<td>\n";
				echo "&nbsp;\n";
				echo "</td>\n";
				echo "<td>\n";

				echo "<span style='display:none;'><input type='radio' name='quelles_classes' id='quelles_classes_photo' value='photo' onclick='verif2()' /></span>\n";

				echo "<label for='quelles_classes_photo' style='cursor: pointer;'>\n";
				echo "<span class='norme'>Aucun élève n'a son Elenoet renseigné.<br />L'affichage des photos n'est donc pas fonctionnel.</span><br />\n";
				echo "</label>\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
		// =====================================================

		/*
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_cpe jec ON jec.e_login=e.login
			where jec.e_login is NULL;";
		*/
		$sql="SELECT DISTINCT login FROM j_eleves_classes jecl
			LEFT JOIN j_eleves_cpe jec ON jecl.login=jec.e_login
			WHERE jec.e_login is null;";
		$test_no_cpe=mysql_query($sql);
		//$test_no_cpe_effectif=mysql_num_rows($test_no_cpe)-mysql_num_rows($test_na);
		$test_no_cpe_effectif=mysql_num_rows($test_no_cpe);
		/*
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_cpe jec ON jec.e_login=e.login
			WHERE jec.e_login is NULL
			AND e_login IN (SELECT DISTINCT login FROM j_eleves_classes);";
		$test_no_cpe_effectif=mysql_query($sql);
		*/
		//if(mysql_num_rows($test_no_cpe)==0){
		if($test_no_cpe_effectif==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_cpe' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves (<i>affectés dans des classes</i>) ont un CPE associé.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_cpe' value='no_cpe' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_cpe' style='cursor: pointer;'>\n";
			//echo "<span class='norme'>Les élèves sans CPE (<i>".mysql_num_rows($test_no_cpe)."</i>).</span><br />\n";
			echo "<span class='norme'>Les élèves (<i>affectés dans des classes</i>) sans CPE (<i>".$test_no_cpe_effectif."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}


		// =====================================================
		$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec
			LEFT JOIN j_eleves_regime jer ON jec.login=jer.login
			WHERE jer.login is null;";
		//echo "$sql<br />";
		$test_no_regime=mysql_query($sql);
		$test_no_regime_effectif=mysql_num_rows($test_no_regime);
		if($test_no_regime_effectif==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_regime' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves (<i>affectés dans des classes</i>) ont le régime renseigné.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td style='vertical-align: top;'>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_regime' value='no_regime' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_regime' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves (<i>affectés dans des classes</i>) dont le régime n'est pas renseigné (<i>".$test_no_regime_effectif."</i>).</span><br />\n";
			echo "<span style='color:red'>Le régime non renseigné est bloquant pour nombre de recherches dans cette page.</span><br />\n";
			if($_SESSION['statut']=='administrateur') {
				echo "<span style='color:red'>";
				echo "Initialiser tous les régimes non renseignés à la valeur&nbsp;: ";
				echo "<a href='".$_SERVER['PHP_SELF']."?initialiser_regimes=d/p".add_token_in_url()."'>demi-pensionnaire</a>, ";
				echo "<a href='".$_SERVER['PHP_SELF']."?initialiser_regimes=int.".add_token_in_url()."'>interne</a>, ";
				echo "<a href='".$_SERVER['PHP_SELF']."?initialiser_regimes=ext.".add_token_in_url()."'>externe</a> ou ";
				echo "<a href='".$_SERVER['PHP_SELF']."?initialiser_regimes=i-e".add_token_in_url()."'>interne-externé</a>";
				echo "</span>";
				echo "<br />\n";
			}
			else {
				echo "<span style='color:red'>Vous pouvez contacter l'administrateur pour initialiser tous les régimes manquants à la valeur de votre choix parmi demi-pensionnaire, interne, externe, interne-externé.</span><br />\n";
			}
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}


		// =====================================================
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_professeurs jep ON jep.login=e.login
			where jep.login is NULL;";
		$test_no_pp=mysql_query($sql);
		$test_no_pp_effectif=mysql_num_rows($test_no_pp)-mysql_num_rows($test_na);
		//if(mysql_num_rows($test_no_pp)==0){
		if($test_no_pp_effectif==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_pp' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves ont un ".getSettingValue('gepi_prof_suivi')." associé.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_pp' value='no_pp' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_pp' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves sans ".getSettingValue('gepi_prof_suivi')." (<i>".$test_no_pp_effectif."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		// =====================================================


		// =====================================================
		/*
		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN responsables2 r ON e.ele_id=r.ele_id
			where r.ele_id is NULL;";
		*/
		$sql="SELECT DISTINCT e.login FROM eleves e,j_eleves_classes jec
				WHERE (e.login=jec.login AND e.ele_id NOT IN (SELECT ele_id FROM responsables2));";
		$test_no_resp=mysql_query($sql);
		//$test_no_resp_effectif=mysql_num_rows($test_no_resp)-mysql_num_rows($test_na);
		$test_no_resp_effectif=mysql_num_rows($test_no_resp);
		//if(mysql_num_rows($test_no_resp)==0){
		if($test_no_resp_effectif==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_resp' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves ont un responsable associé.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_resp' value='no_resp' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_resp' style='cursor: pointer;'>\n";
			//echo "<span class='norme'>Les élèves sans responsable (<i>".$test_no_resp_effectif."</i>).</span><br />\n";
			echo "<span class='norme'>Les élèves sans responsable mais inscrits dans une classe (<i>".$test_no_resp_effectif."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		// =====================================================

		$sql="SELECT 1=1 FROM eleves e
			LEFT JOIN j_eleves_etablissements jee ON jee.id_eleve=e.elenoet
			where jee.id_eleve is NULL;";
		$test_no_etab=mysql_query($sql);
		if(mysql_num_rows($test_no_etab)==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			//echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='no_etab' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves ont leur établissement d'origine renseigné.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_no_etab' value='no_etab' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_no_etab' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves dont l'établissement d'origine n'est pas renseigné (<i>".mysql_num_rows($test_no_etab)."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}


		$sql="SELECT 1=1 FROM eleves WHERE email='';";
		$test_incomplet=mysql_query($sql);
		if(mysql_num_rows($test_incomplet)==0){
			echo "<tr>\n";
			echo "<td>\n";
			echo "&nbsp;\n";
			echo "</td>\n";
			echo "<td>\n";

			echo "<span style='display:none;'><input type='radio' name='quelles_classes' value='email_vide' onclick='verif2()' /></span>\n";

			echo "<span class='norme'>Tous les élèves ont leur email renseigné dans la table 'eleves'.</span><br />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		else{
			echo "<tr>\n";
			echo "<td>\n";
			echo "<input type='radio' name='quelles_classes' id='quelles_classes_email_vide' value='email_vide' onclick='verif2()' />\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "<label for='quelles_classes_email_vide' style='cursor: pointer;'>\n";
			echo "<span class='norme'>Les élèves dont l'email n'est pas renseigné dans la table 'eleves' (<i>".mysql_num_rows($test_incomplet)."</i>).</span><br />\n";
			echo "</label>\n";
			echo "</td>\n";
			echo "</tr>\n";

			// Tester ceux qui ont un compte
		}


		echo "<tr>\n";
		echo "<td>\n";
		echo "<input type='radio' name='quelles_classes' id='quelles_classes_toutes' value='toutes' onclick='verif2()' />\n";
		echo "</td>\n";
		echo "<td>\n";
		echo "<label for='quelles_classes_toutes' style='cursor: pointer;'>\n";
		echo "<span class='norme'>Tous les élèves.</span><br />";
		echo "</label>\n";
		echo "</td>\n";
		echo "</tr>\n";

		$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$nb = mysql_num_rows($classes_list);
		if ($nb !=0) {
			echo "<tr>\n";
			echo "<td valign='top'>\n";

			echo "<input type=\"radio\" name=\"quelles_classes\" id=\"quelles_classes_certaines\" value=\"certaines\" />";

			echo "</td>\n";
			echo "<td valign='top'>\n";

			echo "<label for='quelles_classes_certaines' style='cursor: pointer;'>\n";
			echo "<span class = \"norme\">Seulement les élèves des classes sélectionnées ci-dessous : </span>";
			echo "</label>\n";
			echo "<br />\n";

				$nb_class_par_colonne=round($nb/3);
				//echo "<table width='100%' border='1'>\n";
				echo "<table width='100%' summary='Choix des classes'>\n";
				echo "<tr valign='top' align='center'>\n";

				$i = '0';

				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";

				while ($i < $nb) {
					$id_classe = mysql_result($classes_list, $i, 'id');
					$temp = "case_".$id_classe;
					$classe = mysql_result($classes_list, $i, 'classe');
	
					if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					//echo "<label id='label_tab_id_classe_$i' for='tab_id_classe_$i' style='cursor: pointer;'>";
					//echo "<input type='checkbox' name='$temp' id='tab_id_classe_$i' value='yes' onclick=\"verif1()\" onchange='change_style_classe($i)' />";

					echo "<label id='label_tab_id_classe_$id_classe' for='tab_id_classe_$id_classe' style='cursor: pointer;'>";
					echo "<input type='checkbox' name='$temp' id='tab_id_classe_$id_classe' value='yes' onclick=\"verif1()\" onchange='change_style_classe($id_classe)' />";
					echo "Classe : $classe</label><br />\n";
	
					$i++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";

		echo "<script type='text/javascript'>
	function change_style_classe(num) {
		if(document.getElementById('tab_id_classe_'+num)) {
			if(document.getElementById('tab_id_classe_'+num).checked) {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_tab_id_classe_'+num).style.fontWeight='normal';
			}
		}
	}

	if(document.getElementById('motif_rech_nom')) {
		document.getElementById('motif_rech_nom').focus();
	}
</script>\n";


		echo "<input type='hidden' name='is_posted' value='2' />\n";

		echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
		echo "</form>\n";


	}
//} else {
}

if(isset($quelles_classes)) {
	//echo "$quelles_classes<br />";

	echo "<p class='small'><em>Remarque&nbsp;:</em> l'identifiant mentionné ici ne permet pas aux élèves de se connecter à Gepi, il sert simplement d'identifiant unique.";
	//if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	if($_SESSION['statut']=="administrateur") {
		echo " Pour permettre aux élèves de se connecter à Gepi, vous devez leur créer des comptes d'accès, en passant par la page Gestion des bases -> Gestion des comptes d'accès utilisateurs -> <a href='../utilisateurs/edit_eleve.php'>Elèves</a>.";
	}
	elseif($_SESSION['statut']=="scolarite") {
		echo " Pour permettre aux élèves de se connecter à Gepi, vous devez vous connecter en 'administrateur' et leur créer des comptes d'accès, en passant par la page Gestion des bases -> Gestion des comptes d'accès utilisateurs -> Elèves.";
	}
	echo "</p>\n";


	echo "<form enctype=\"multipart/form-data\" action=\"index.php\" method=\"post\">\n";
	if (!isset($order_type)) { $order_type='nom,prenom';}

	echo add_token_field();

	/*
	echo "<table border='1' cellpadding='2' class='boireaus'>\n";
	echo "<tr>\n";
	echo "<td><p>Identifiant</p></td>\n";
	echo "<td><p><a href='index.php?order_type=nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Nom Prénom</a></p></td>\n";
	echo "<td><p><a href='index.php?order_type=sexe,nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Sexe</a></p></td>\n";
	echo "<td><p><a href='index.php?order_type=naissance,nom,prenom&amp;quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	echo "'>Date de naissance</a></p></td>\n";
	if ($quelles_classes == 'na') {
		echo "<td><p>Classe</p></td>\n";
	} else {
		echo "<td><p><a href='index.php?order_type=classe,nom,prenom&amp;quelles_classes=$quelles_classes";
		if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
		echo "'>Classe</a></p></td>\n";
	}
//    echo "<td><p>Classe</p></td>";
	echo "<td><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></td>\n";
	echo "<td><p><input type='submit' value='Supprimer' onclick=\"return confirmlink(this, 'La suppression d\'un élève est irréversible et entraîne l\'effacement complet de toutes ses données (notes, appréciations, ...). Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\" /></p></td>\n";
	if (getSettingValue("active_module_trombinoscopes")=='y') {
		echo "<td><p><input type='submit' value='Télécharger les photos' name='bouton1' /></td>\n";
	}
	echo "</tr>\n";
	*/

	include("index_call_data.php");

	$csv="";
	echo "<table border='1' cellpadding='2' class='boireaus'  summary='Tableau des élèves de la classe'>\n";
	echo "<tr>\n";
	echo "<th><p>Identifiant</p></th>\n";
	$csv.="Identifiant;";

	$ajout_param_lien="";
	if(isset($motif_rech)){$ajout_param_lien.="&amp;motif_rech=$motif_rech";}
	if(isset($mode_rech_nom)){$ajout_param_lien.="&amp;mode_rech_nom=$mode_rech_nom";}
	if(isset($mode_rech_prenom)){$ajout_param_lien.="&amp;mode_rech_prenom=$mode_rech_prenom";}
	//if((isset($mode_rech_champ))&&(isset($champ_rech))) {$ajout_param_lien.="&amp;mode_rech_champ=$mode_rech_champ&amp;champ_rech=$champ_rech";}
	if(isset($mode_rech)) {$ajout_param_lien.="&amp;mode_rech=$mode_rech";}
	if(isset($mode_rech_elenoet)) {$ajout_param_lien.="&amp;mode_rech_elenoet=$mode_rech_elenoet";}
	if(isset($mode_rech_ele_id)) {$ajout_param_lien.="&amp;mode_rech_ele_id=$mode_rech_ele_id";}
	if(isset($mode_rech_no_gep)) {$ajout_param_lien.="&amp;mode_rech_no_gep=$mode_rech_no_gep";}
	// 20130607
	if(isset($quelles_classes_rech_mef)) {$ajout_param_lien.="&amp;motif_rech_mef=$motif_rech_mef";}

	echo "<th><p><a href='index.php?order_type=nom,prenom&amp;quelles_classes=$quelles_classes";
	echo $ajout_param_lien;
	echo "'>Nom Prénom</a></p></th>\n";
	$csv.="Nom Prénom;";
	$csv.="Date sortie;";

	echo "<th><p><a href='index.php?order_type=sexe,nom,prenom&amp;quelles_classes=$quelles_classes";
	echo $ajout_param_lien;
	echo "'>Sexe</a></p></th>\n";
	$csv.="Sexe;"
	;
	echo "<th><p><a href='index.php?order_type=naissance,nom,prenom&amp;quelles_classes=$quelles_classes";
	echo $ajout_param_lien;
	echo "'>Date de naissance</a></p></th>\n";
	$csv.="Date de naissance;";

	echo "<th><p><a href='index.php?order_type=regime,nom,prenom&amp;quelles_classes=$quelles_classes";
	echo $ajout_param_lien;
	echo "'>Régime</a></p></th>\n";
	$csv.="Régime;";

	if (($quelles_classes == 'na')||($quelles_classes == 'dse')) {
		echo "<th><p>Classe</p></th>\n";
	} else {
		echo "<th><p>";
		if($_SESSION['statut'] != 'professeur') {
			echo "<a href='index.php?order_type=classe,nom,prenom&amp;quelles_classes=$quelles_classes";
			echo $ajout_param_lien;
			echo "'>Classe</a>";
		}
		else{
			echo "Classe";
		}
		echo "</p></th>\n";
	}
	$csv.="Classe;";

	// 20130607
	echo "<th><p>MEF</p></th>\n";

//    echo "<th><p>Classe</p></th>";
	echo "<th><p>Enseign.<br />suivis</p></th>\n";
	//$csv.=";";
	echo "<th><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></th>\n";
	$csv.=ucfirst(getSettingValue("gepi_prof_suivi")).";";

	//if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
	if($_SESSION['statut']=="administrateur") {
		echo "<th><p><input type='submit' value='Supprimer' onclick=\"return confirmlink(this, 'La suppression d\'un élève est irréversible et entraîne l\'effacement complet de toutes ses données (notes, appréciations, ...). Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\" /></p></th>\n";
	}
	elseif($_SESSION['statut']=="scolarite") {
		echo "<th><p><span title=\"La suppression n'est possible qu'avec un compte administrateur\">Supprimer</span></p></th>\n";
	}
	//$csv.=";";

	if (getSettingValue("active_module_trombinoscopes")=='y') {
		if($_SESSION['statut']=="professeur") {
			if (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes') {
				echo "<th><p><input type='submit' value='Téléverser les photos' name='bouton1' /></th>\n";
			}
		}
		else{
			echo "<th><p><input type='submit' value='Téléverser les photos' name='bouton1' /></th>\n";
		}
	}
	//$csv.=";";

	echo "</tr>\n";
	$csv.="\r\n";

	if(!isset($tab_eleve)){
		$nombreligne = mysql_num_rows($calldata);
	}
	else{
		$nombreligne = count($tab_eleve);
	}
	//echo "\$nombreligne=$nombreligne<br />";
/*
	echo "<p>Total : $nombreligne éleves</p>\n";
	echo "<p>Remarque : le login ne permet pas aux élèves de se connecter à Gepi. Il sert simplement d'identifiant unique.</p>\n";
*/

	$acces_class_const=acces("/classes/classes_const.php", $_SESSION['statut']);

	$tab_mef=get_tab_mef();
	$acces_associer_eleve_mef=acces("/mef/associer_eleve_mef.php", $_SESSION['statut']);

	$i = 0;
	$alt=1;
	while ($i < $nombreligne){
		if(!isset($tab_eleve[$i])){
			$eleve_login = mysql_result($calldata, $i, "login");
			$eleve_nom = mysql_result($calldata, $i, "nom");
			$eleve_prenom = mysql_result($calldata, $i, "prenom");
			$eleve_sexe = mysql_result($calldata, $i, "sexe");
			$eleve_naissance = mysql_result($calldata, $i, "naissance");
			$elenoet = mysql_result($calldata, $i, "elenoet");
			$date_sortie_elv = mysql_result($calldata, $i, "date_sortie");
			// 20130607
			$mef_code = mysql_result($calldata, $i, "mef_code");
			if($quelles_classes=='no_regime') {
				$eleve_regime = "-";
				$eleve_doublant =  "-";
			}
			else {
				$eleve_regime =  mysql_result($calldata, $i, "regime");
				$eleve_doublant =  mysql_result($calldata, $i, "doublant");
			}
		}
		else{
			$eleve_login = $tab_eleve[$i]["login"];
			$eleve_nom = $tab_eleve[$i]["nom"];
			$eleve_prenom = $tab_eleve[$i]["prenom"];
			$eleve_sexe = $tab_eleve[$i]["sexe"];
			$eleve_naissance = $tab_eleve[$i]["naissance"];
			$elenoet =  $tab_eleve[$i]["elenoet"];
			$eleve_regime =  $tab_eleve[$i]["regime"];
			$eleve_doublant =  $tab_eleve[$i]["doublant"];
			//$date_sortie_elv = mysql_result($calldata, $i, "date_sortie");
			$date_sortie_elv = $tab_eleve[$i]["date_sortie"];
			// 20130607
			$mef_code = $tab_eleve[$i]["mef_code"];
		}

		$call_classe = mysql_query("SELECT n.classe, n.id FROM j_eleves_classes c, classes n WHERE (c.login ='$eleve_login' and c.id_classe = n.id) order by c.periode DESC");
		$eleve_classe = @mysql_result($call_classe, 0, "classe");
		$eleve_id_classe = @mysql_result($call_classe, 0, "id");
		$pas_de_classe="n";
		if ($eleve_classe == '') {
			$eleve_classe = "<font color='red'>N/A</font>";
			$eleve_classe_csv = "N/A";
			$pas_de_classe="y";
		}
		else {
			$eleve_classe_csv = $eleve_classe;
		}

		$call_suivi = mysql_query("SELECT u.* FROM utilisateurs u, j_eleves_professeurs s WHERE (s.login ='$eleve_login' and s.professeur = u.login and s.id_classe='$eleve_id_classe')");
		if(mysql_num_rows($call_suivi)==0){
			$eleve_profsuivi_nom = "";
			$eleve_profsuivi_prenom = "";
		}
		else{
			$eleve_profsuivi_nom = @mysql_result($call_suivi, 0, "nom");
			$eleve_profsuivi_prenom = @mysql_result($call_suivi, 0, "prenom");
		}

		if ($eleve_profsuivi_nom == '') {
			if(($acces_class_const)&&($eleve_id_classe!="")) {
				$eleve_profsuivi_nom = "<a href='../classes/classes_const.php?id_classe=".$eleve_id_classe."' title=\"Définir le ".$gepi_prof_suivi."\"><font color='red'>N/A</font></a>";
			}
			else {
				$eleve_profsuivi_nom = "<font color='red'>N/A</font>";
			}
			$info_pp=$eleve_profsuivi_nom;

			$eleve_profsuivi_nom_csv = "N/A";
		}
		else {
			$eleve_profsuivi_nom_csv = $eleve_profsuivi_nom;
			$info_pp=casse_mot($eleve_profsuivi_nom,"maj")." ".casse_mot($eleve_profsuivi_prenom,"majf2");
		}
		//$delete_login = 'delete_'.$eleve_login;

		//========================================
		// Début de l'affichage de la ligne élève:
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";

		echo "<td><p>" . $eleve_login . "</p></td>\n";
		$csv.="$eleve_login;";

		echo "<td>";

		if($_SESSION['statut']=='administrateur') {$avec_lien="y";}
		else {$avec_lien="n";}
		$lien_image_compte_utilisateur=lien_image_compte_utilisateur($eleve_login, "eleve", "", $avec_lien);
		if($lien_image_compte_utilisateur!="") {echo "<div style='float:right; width: 16px'>".$lien_image_compte_utilisateur."</div>";}

		if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='autre')||
			(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiAccesTouteFicheEleveCpe')))||
			(($_SESSION['statut']=='cpe')&&(is_cpe($_SESSION['login'],'',$eleve_login)))||
			(($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login'],"",$eleve_login))&&(getSettingAOui('GepiAccesGestElevesProfP')))||
			((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $quelles_classes)))) {
			echo "<p><a href='modify_eleve.php?eleve_login=$eleve_login&amp;quelles_classes=$quelles_classes&amp;order_type=$order_type";
			if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
			if(isset($mode_rech)){echo "&amp;mode_rech=$mode_rech";}
			echo "'>$eleve_nom $eleve_prenom</a>";
		}
		else {
			echo "$eleve_nom $eleve_prenom";
		}
		$csv.="$eleve_nom $eleve_prenom;";

		if ($date_sortie_elv!=0) {
		     echo "<br/>";
		     echo "<span class=\"red\"><b>Sortie le ".affiche_date_sortie($date_sortie_elv)."</b></span>";

			$csv.=$date_sortie_elv;
		}
		echo "</p></td>\n";
		$csv.=";";

		// Sexe
		echo "<td><p>$eleve_sexe</p></td>\n";
		$csv.="$eleve_sexe;";

		// Naissance
		echo "<td><p>".affiche_date_naissance($eleve_naissance)."</p></td>\n";
		$csv.=affiche_date_naissance($eleve_naissance).";";

		// Régime
		echo "<td><p>";
		if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
			echo "<a href='#' onclick=\"afficher_changement_regime('$eleve_login', '$eleve_regime') ;return false;\">";
			echo "<span id='regime_$eleve_login'>";
			echo $eleve_regime;
			echo "</span>";
			echo "</a>";
		}
		else {
			echo $eleve_regime;
		}
		echo "</p></td>\n";
		$csv.="$eleve_regime;";

		// Classe(s)
		if(($_SESSION['statut']=='administrateur')&&($pas_de_classe!="y")) {
			echo "<td><p><a href='../classes/classes_const.php?id_classe=$eleve_id_classe'>$eleve_classe</a></p></td>\n";
		}
		else {
			if(acces('/classes/ajout_eleve_classe.php', $_SESSION['statut'])) {
				echo "<td><p><a href=\"javascript:affiche_ajout_ele_clas('$eleve_login')\" title=\"Inscrire $eleve_nom $eleve_prenom dans une classe.\">$eleve_classe</a></p></td>\n";
			}
			else {
				echo "<td><p>$eleve_classe</p></td>\n";
			}
		}
		$csv.="$eleve_classe_csv;";

		// MEF
		echo "<td><p style='font-size:x-small;'>";
		if($acces_associer_eleve_mef) {
			echo "<a href='../mef/associer_eleve_mef.php?type_selection=nom_eleve&amp;nom_eleve=".$eleve_nom."' target='_blank'>";
		}
		if(isset($tab_mef[$mef_code])) {
			echo $tab_mef[$mef_code]['designation_courte'];
		}
		else {
			echo $mef_code;
		}
		if($acces_associer_eleve_mef) {
			echo "</a>";
		}
		echo "</p></td>\n";
		//$csv.=";";

		// Enseignements suivis
		echo "<td><p><a href='../classes/eleve_options.php?login_eleve=".$eleve_login."&amp;id_classe=$eleve_id_classe&amp;quitter_la_page=y' target='_blank'><img src='../images/icons/chercher.png' width='16' height='16' alt='Enseignements suivis' title='Enseignements suivis' /></a></p></td>\n";
		//$csv.=";";

		// Professeur principal
		// 20130802
		if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
			echo "<td><p>";
			echo "<a href='#' onclick=\"afficher_changement_prof_suivi('$eleve_login') ;return false;\">";
			echo "<span id='prof_suivi_$eleve_login'>";
			echo $info_pp;
			echo "</span>";
			echo "</a>";
			echo "</p></td>\n";
		}
		else {
			echo "<td><p>$info_pp</p></td>\n";
		}
		$csv.="$info_pp;";

		//if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
		if($_SESSION['statut']=="administrateur") {
			//echo "<td><p><center><INPUT TYPE=CHECKBOX NAME='$delete_login' VALUE='yes' /></center></p></td></tr>\n";
			//echo "<td><p align='center'><input type='checkbox' name='$delete_login' value='yes' /></p></td>\n";
			echo "<td><p align='center'><input type='checkbox' name='delete_eleve[]' value='$eleve_login' /></p></td>\n";
		}
		elseif($_SESSION['statut']=="scolarite") {
			echo "<td><p align='center'><span title=\"La suppression n'est possible qu'avec un compte administrateur\">-</span></p></td>\n";
		}

		if ((getSettingValue("active_module_trombinoscopes")=='y')&&
			((($_SESSION['statut']=="professeur")&&(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes'))||
				($_SESSION['statut']!="professeur"))) {

			//echo "<td style='white-space: nowrap;'><input name='photo[$i]' type='file' />\n";
			echo "<td style='white-space: nowrap; text-align:center;'>\n";

			//echo "<input name='photo[$i]' type='file' />\n";

			// Dans le cas du multisite, on préfère le login pour afficher les photos
			$nom_photo_test = (isset ($multisite) AND $multisite == 'y') ? $eleve_login : $elenoet;
			echo "<input type='hidden' name='quiestce[$i]' value=\"$nom_photo_test\" />\n";

			$photo=nom_photo($elenoet);
			$temoin_photo="";
			if($photo){
				echo "<div style='width: 32px; height: 32px; float:right;'>";

				$titre="$eleve_nom $eleve_prenom";

				$texte="<div align='center'>\n";
				$texte.="<img src='".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
				$texte.="<br />\n";
				$texte.="</div>\n";

				$temoin_photo="y";

				$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

				echo "<a href='".$photo."' target='_blank' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-20,20,500,40,30);\">";

				echo "<img src='../mod_trombinoscopes/images/";
				if($eleve_sexe=="F") {
					echo "photo_f.png";
				}
				else{
					echo "photo_g.png";
				}
				echo "' width='32' height='32'  align='middle' border='0' alt='photo présente' title='photo présente' />";
				echo "</a>";

				echo "</div>";
			}


			if($nom_photo_test=="") {
				// Dans le cas multisite, le login élève est forcément renseigné
				echo "<span style='color:red'>Elenoet non renseigné</span>";
			}
			else {
				//echo "<span id='span_file_$i'></span>";
				echo "<span id='span_file_$i'>";
				//echo "<a href='javascript:add_file_upload($i)'><img src='../images/ico_edit16plus.png' width='16' height='16' alt='Choisir un fichier à uploader' /></a>";
				// Pour que si JavaScript est désactivé, on ait quand même le champ FILE
				echo "<input name='photo[$i]' type='file' />\n";
				echo "</span>";
			}

			echo "</td>\n";
		}

		echo "</tr>\n";
		$csv.="\r\n";

		$i++;
	}
	echo "</table>\n";
	echo "<p>Total : $nombreligne élève";
	if($nombreligne>1) {echo "s";}

	//echo " - <a href='".$_SERVER['PHP_SELF']."?csv=".urlencode($csv).add_token_in_url()."'>CSV</a>\n";
	$fichier_csv="../temp/".get_user_temp_directory()."/liste_eleves_".strftime("%Y%m%d_%H%M%S").".csv";
	$f=fopen($fichier_csv, "w+");
	fwrite($f, $csv);
	fclose($f);
	echo " - <a href='$fichier_csv'>CSV</a>\n";
	echo "</p>\n";

	echo "<script type='text/javascript'>
	// Ajout d'un champ FILE... pour éviter la limite de max_file_uploads (on n'a que le nombre de champs FILE correspondant à ce que l'on souhaite effectivement uploader
	function add_file_upload(i) {
		if(document.getElementById('span_file_'+i)) {
			document.getElementById('span_file_'+i).innerHTML='<input type=\'file\' name=\'photo['+i+']\' />';
		}
	}

	// On remplace les champs FILE par des liens d'ajout de champ FILE... au cas où JavaScript serait désactivé.
	for(i=0;i<$i;i++) {
		if(document.getElementById('span_file_'+i)) {
			document.getElementById('span_file_'+i).innerHTML='<a href=\'javascript:add_file_upload('+i+')\'><img src=\'../images/ico_edit16plus.png\' width=\'16\' height=\'16\' alt=\'Choisir un fichier à uploader\' /></a>';
		}
	}

	function affiche_ajout_ele_clas(login_ele) {
		if(document.getElementById('div_form_ajout_ele_clas')) {
			if(document.getElementById('login_ele_ajout_classe')) {
				document.getElementById('login_ele_ajout_classe').value=login_ele;
				afficher_div('div_form_ajout_ele_clas', 'y',-20,20);
			}
		}
	}

	function update_champs_periode() {
		if(document.getElementById('form_ajout_ele_clas_id_classe')) {
			id_classe=document.getElementById('form_ajout_ele_clas_id_classe').options[document.getElementById('form_ajout_ele_clas_id_classe').selectedIndex].value;
			//alert(id_classe);
			if((id_classe!='')&&(document.getElementById('span_periodes'))) {
				new Ajax.Updater($('span_periodes'),'index.php?id_classe='+id_classe+'&mode=update_champs_periode".add_token_in_url(false)."',{method: 'get'});
			}
		}
	}

	function afficher_changement_prof_suivi(login_ele) {
		if(document.getElementById('prof_suivi_'+login_ele)) {
			new Ajax.Updater($('span_choix_prof_suivi'),'index.php?login_ele='+login_ele+'&mode=update_champs_choix_prof_suivi".add_token_in_url(false)."',{method: 'get'});

			document.getElementById('login_ele_prof_suivi').value=login_ele;
			afficher_div('div_form_choix_prof_suivi_ele', 'y',-20,20);
		}
	}

	function modifier_prof_suivi() {
		login_ele=document.getElementById('login_ele_prof_suivi').value;
		//alert(login_ele);
		if(document.getElementById('prof_suivi_choisi')) {
			prof_suivi=document.getElementById('prof_suivi_choisi').options[document.getElementById('prof_suivi_choisi').selectedIndex].value;
			//alert(prof_suivi);

			if($('prof_suivi_'+login_ele)) {
				new Ajax.Updater($('prof_suivi_'+login_ele),'index.php?login_ele='+login_ele+'&prof_suivi='+prof_suivi+'&mode=modif_prof_suivi".add_token_in_url(false)."',{method: 'get'});
				cacher_div('div_form_choix_prof_suivi_ele');
			}
		}
	}
</script>\n";

	echo "<input type='hidden' name='quelles_classes' value='$quelles_classes' />\n";
	// Dans le cas scolarite, la liste des classes est dans la table tempo
	if(isset($motif_rech)){
		echo "<input type='hidden' name='motif_rech' value='$motif_rech' />\n";
	}
	if(isset($mode_rech)){
		echo "<input type='hidden' name='mode_rech' value='$mode_rech' />\n";
	}
	echo "<input type='hidden' name='order_type' value='$order_type' />\n";

	?>
	<!--/table-->
	<input type="hidden" name="is_posted" value="1" />
<?php
// pour le trombinoscope on met la taille maximale d'une photo
?>
	<input type="hidden" name="MAX_FILE_SIZE" value="150000" />
	<input type="hidden" name="action" value="depot_photo" />
	<input type="hidden" name="total_photo" value="<?php echo $nombreligne; ?>" />
	</form>

	<?php

	//=========================
	$sql="SELECT max(num_periode) AS max_per FROM classes c, periodes p WHERE p.id_classe=c.id;";
	$res_per=mysql_query($sql);
	$max_per=mysql_result($res_per, 0, 'max_per');

	$sql="SELECT id, classe, nom_complet FROM classes ORDER BY classe, nom_complet;";
	$res_classe=mysql_query($sql);

	$titre_infobulle="Inscription dans une classe";
	$texte_infobulle="<form action='../classes/ajout_eleve_classe.php' method='post'>
	".add_token_field()."
	<input type='hidden' name='login_ele_ajout_classe' id='login_ele_ajout_classe' value='' />
	<p style='text-align:center;'>Choisissez une classe&nbsp;: 
	<select name='id_classe' id='form_ajout_ele_clas_id_classe' onchange='update_champs_periode()'>
		<option value=''>---</option>";
	while($lig_classe=mysql_fetch_object($res_classe)) {
		$texte_infobulle.="
		<option value='$lig_classe->id'>$lig_classe->classe ($lig_classe->nom_complet)</option>";
	}
	$texte_infobulle.="
	</select>
	<br />
	et la ou les périodes<br />
	<span id='span_periodes'>";
	for($loop=1;$loop<=$max_per;$loop++) {
		$texte_infobulle.="
		<input type='checkbox' id='num_periode_$loop' name='num_periode[]' value='$loop' /><label for='num_periode_$loop'>Période $loop</label><br />";
	}
	$texte_infobulle.="
	</span><br />
	<input type='submit' value='Inscrire' />
</form>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_form_ajout_ele_clas',$titre_infobulle,"",$texte_infobulle,"",20,0,'y','y','n','n');
	//=========================
	$titre_infobulle="Choix du ".getSettingValue('gepi_prof_suivi');
	$texte_infobulle="<form action='./index.php' method='post'>
	".add_token_field()."
	<input type='hidden' name='mode' id='modif_prof_suivi' value='' />
	<input type='hidden' name='login_ele_prof_suivi' id='login_ele_prof_suivi' value='' />
	<p style='text-align:center;'>Choisissez un ".getSettingValue('gepi_prof_suivi')."&nbsp;: 
	<span id='span_choix_prof_suivi'></span><br />
	<input type='button' value='Valider' onclick=\"modifier_prof_suivi()\" />
</form>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_form_choix_prof_suivi_ele',$titre_infobulle,"",$texte_infobulle,"",20,0,'y','y','n','n');
	//=========================

	echo "<br />\n";
	$temoin_notes_bas_de_page="n";
	$max_file_uploads=ini_get('max_file_uploads');
	if(($max_file_uploads!="")&&(mb_strlen(preg_replace("/[^0-9]/","",$max_file_uploads))==mb_strlen($max_file_uploads))&&($max_file_uploads>0)) {
		echo "<p><i>Notes</i>&nbsp;:</p>\n";
		echo "<ul>\n";
		echo "<li><p>L'upload des photos est limité à $max_file_uploads fichier(s) simultanément.</p></li>\n";
		$temoin_notes_bas_de_page="y";
	}

	if($_SESSION['statut']=='administrateur') {
		if($temoin_notes_bas_de_page=="n") {
			echo "<p><i>Notes</i>&nbsp;:</p>\n";
			echo "<ul>\n";
		}
		echo "<li><p>Il est possible d'uploader un fichier <a href='../mod_trombinoscopes/trombinoscopes_admin.php#telecharger_photos_eleves'>ZIP d'un lot de photos</a> plutôt que les uploader une par une.</p></li>\n";
		$temoin_notes_bas_de_page="y";
	}

	if($temoin_notes_bas_de_page=="y") {
		echo "</ul>\n";
	}






	//========================================================
	echo "<div id='div_changer_regime' style='position: absolute; top: 220px; right: 20px; width: 200px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
	
		echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 200px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_changer_regime')\">\n";
			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
			echo "<a href='#' onClick=\"cacher_div('div_changer_regime');return false;\">\n";
			echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
			echo "</a>\n";
			echo "</div>\n";

			echo "<div id='titre_entete_changer_regime'>AA</div>\n";
		echo "</div>\n";
		
		echo "<div id='corps_changer_regime' class='infobulle_corps' style='color: #000000; cursor: auto; padding: 0px; height: 7em; width: 200px; overflow: auto;'>";


		$tab_regime=array('d/p', 'ext.', 'int.','i-e');
		echo "<form name='form_changer_regime' id='form_changer_regime' action ='ajax_modif_eleve.php' method='post' target='_blank'>\n";
		echo "<input type='hidden' name='regime_login_eleve' id='regime_login_eleve' value='' />\n";
		for($loop=0;$loop<count($tab_regime);$loop++) {
			echo "<input type='radio' name='regime_regime_eleve' id='regime_regime_eleve_$loop' value='".$tab_regime[$loop]."' ";
			//if($eleve_regime==$tab_regime[$loop]) {}
			echo "/><label for='regime_regime_eleve_$loop'> $tab_regime[$loop]</label><br />\n";
		}
		echo add_token_field();
		echo "<input type='button' onclick='valider_changement_regime()' name='Valider' value='Valider' />\n";
		echo "</form>\n";

		echo "</div>\n";
	
	echo "</div>\n";


	echo "<script type='text/javascript'>

	function afficher_changement_regime(login_eleve, regime_eleve) {
		// regime_eleve est le régime actuel de l'élève
		document.getElementById('titre_entete_changer_regime').innerHTML='Régime de '+login_eleve;
		document.getElementById('regime_login_eleve').value=login_eleve;

		//alert('regime_eleve='+regime_eleve);
		for(i=0;i<".count($tab_regime).";i++) {
			//alert('regime_eleve='+regime_eleve);
			if(regime_eleve==document.getElementById('regime_regime_eleve_'+i).value) {
				document.getElementById('regime_regime_eleve_'+i).checked=true;
			}
		}

		afficher_div('div_changer_regime','y',-20,20);
	}


	function valider_changement_regime() {
		if(document.getElementById('regime_login_eleve')) {
			login_eleve=document.getElementById('regime_login_eleve').value;

			for (var i=0; i<document.forms['form_changer_regime'].regime_regime_eleve.length;i++) {
				if (document.forms['form_changer_regime'].regime_regime_eleve[i].checked) {
					regime_eleve=document.forms['form_changer_regime'].regime_regime_eleve[i].value;
				}
			}

			//alert(regime_eleve);

			new Ajax.Updater($('regime_'+login_eleve),'ajax_modif_eleve.php?login_eleve='+login_eleve+'&regime_eleve='+regime_eleve+'&mode=changer_regime".add_token_in_url(false)."',{method: 'get'});
		}
		else {
			alert('document.getElementById(\'regime_login_eleve\') n est pas affecté.')
		}

		cacher_div('div_changer_regime');

	}
</script>\n";
	//========================================================


}
require("../lib/footer.inc.php");
?>
