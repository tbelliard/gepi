<?php

/*
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql = "SELECT 1=1 FROM droits WHERE id='/mod_discipline/saisie_avertissement_fin_periode.php';";
$test = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($test) == 0) {
	$sql = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_avertissement_fin_periode.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Discipline: Saisie des sanctions/avertissements de fin de période', '');";
	$insert = mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_discipline')) {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

//require('sanctions_func_lib.php');

$mod_disc_terme_avertissement_fin_periode=getSettingValue('mod_disc_terme_avertissement_fin_periode');
if($mod_disc_terme_avertissement_fin_periode=="") {$mod_disc_terme_avertissement_fin_periode="avertissement de fin de période";}

if(preg_match("/^[AEIOUY]/i", ensure_ascii($mod_disc_terme_avertissement_fin_periode))) {
	$prefixe_mod_disc_terme_avertissement_fin_periode_de="d'";
	$prefixe_mod_disc_terme_avertissement_fin_periode_le="l'";
}
else {
	$prefixe_mod_disc_terme_avertissement_fin_periode_de="de ";
	$prefixe_mod_disc_terme_avertissement_fin_periode_le="le ";
}

$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$mode_js=isset($_POST['mode_js']) ? $_POST['mode_js'] : (isset($_GET['mode_js']) ? $_GET['mode_js'] : "n");
$lien_refermer=isset($_POST['lien_refermer']) ? $_POST['lien_refermer'] : (isset($_GET['lien_refermer']) ? $_GET['lien_refermer'] : "n");

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

/*
if((!isset($periode))||(!isset($login_ele))) {
	$mess=rawurlencode("L'élève ou la période n'a pas été choisie !");
	header("Location: ../accueil.php?msg=$mess");
	die();
}
*/

//debug_var();

if((isset($periode))&&(isset($login_ele))) {
	if($_SESSION['statut']=='professeur') {
		if((!getSettingAOui('saisieDiscProfPAvt'))||(!is_pp($_SESSION['login'], "", $login_ele))) {
			$mess=rawurlencode("Vous n'êtes pas ".getSettingValue('gepi_prof_suivi')." de ".get_nom_prenom_eleve($login_ele)." ou bien vous n'êtes pas autorisé à saisir les ".$mod_disc_terme_avertissement_fin_periode."s !");
			tentative_intrusion(1, "Tentative d'accès à la saisie ".$prefixe_mod_disc_terme_avertissement_fin_periode_de."$mod_disc_terme_avertissement_fin_periode pour l'élève ".get_nom_prenom_eleve($login_ele).".");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		if(!getSettingAOui('GepiRubConseilScol')) {
			$mess=rawurlencode("Vous n'êtes pas autorisé à saisir l'avis du bulletin !");
			tentative_intrusion(1, "Tentative d'accès à la saisie ".$prefixe_mod_disc_terme_avertissement_fin_periode_de."$mod_disc_terme_avertissement_fin_periode pour l'élève ".get_nom_prenom_eleve($login_ele).".");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}
	}
	elseif($_SESSION['statut']=='cpe') {
		$acces_suite="n";
		if(getSettingAOui('saisieDiscCpeAvtTous')) {
			$acces_suite="y";
		}
		elseif((!getSettingAOui('saisieDiscCpeAvt'))&&(is_cpe($_SESSION['login'], "", $login_ele))) {
			$acces_suite="y";
		}
		else {
			$mess=rawurlencode("Vous n'êtes pas CPE de ".get_nom_prenom_eleve($login_ele)." ou bien vous n'êtes pas autorisé à saisir les ".$mod_disc_terme_avertissement_fin_periode."s !");
			tentative_intrusion(1, "Tentative d'accès à la saisie ".$prefixe_mod_disc_terme_avertissement_fin_periode_de."$mod_disc_terme_avertissement_fin_periode pour l'élève ".get_nom_prenom_eleve($login_ele).".");
			header("Location: ../accueil.php?msg=$mess");
			die();
		}
	}

	$msg="";

	// 20140616
	if(isset($_POST['get_avertissement_fin_periode'])) {
		check_token();

		//$tab_type_avertissement_fin_periode=get_tab_type_avertissement();

		//$tab_av_ele=get_tab_avertissement($login_ele, $periode);

		echo champs_checkbox_avertissements_fin_periode($login_ele, $periode);

		die();
	}

	if(isset($_POST['saisie_avertissement_fin_periode'])) {
		check_token();

		$etat_verrouillage_eleve_periode=etat_verrouillage_eleve_periode($login_ele, $periode);
		if(($etat_verrouillage_eleve_periode=="N")||($etat_verrouillage_eleve_periode=="P")) {
			$id_type_avertissement=isset($_POST['id_type_avertissement']) ? $_POST['id_type_avertissement'] : array();

			if(!is_array($id_type_avertissement)) {
				//echo "\$id_type_avertissement n'est pas un tableau : '$id_type_avertissement'<br />";
				$tmp_chaine=$id_type_avertissement;
				$id_type_avertissement=explode("|", $tmp_chaine);
			}

			/*
			echo "<p>\$id_type_avertissement<pre>";
			print_r($id_type_avertissement);
			echo "</pre>";
			*/

			$nb_err=0;
			$tab_av_ele=get_tab_avertissement($login_ele, $periode);
			if(isset($tab_av_ele['id_type_avertissement'][$periode])) {
				for($loop=0;$loop<count($tab_av_ele['id_type_avertissement'][$periode]);$loop++) {
					if(!in_array($tab_av_ele['id_type_avertissement'][$periode][$loop], $id_type_avertissement)) {
						$sql="DELETE FROM s_avertissements WHERE login_ele='$login_ele' AND periode='$periode' AND id_type_avertissement='".$tab_av_ele['id_type_avertissement'][$periode][$loop]."';";
						//$msg.="$sql<br />";
						//echo "$sql<br />";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$del) {
							$msg.="Erreur lors de la suppression de ".$mod_disc_terme_avertissement_fin_periode.".";
							$nb_err++;
						}
					}
				}
			}

			$tab_type_avertissement_fin_periode=get_tab_type_avertissement();

			for($loop=0;$loop<count($id_type_avertissement);$loop++) {
				if((preg_match("/^[0-9]{1,}$/", $id_type_avertissement[$loop]))&&
					(array_key_exists($id_type_avertissement[$loop] ,$tab_type_avertissement_fin_periode['id_type_avertissement']))) {
					if((!isset($tab_av_ele['id_type_avertissement'][$periode]))||
					((!in_array($id_type_avertissement[$loop], $tab_av_ele['id_type_avertissement'][$periode])))) {
						$sql="INSERT INTO s_avertissements SET login_ele='$login_ele', 
												periode='$periode', 
												id_type_avertissement='".$id_type_avertissement[$loop]."',
												declarant='".$_SESSION['login']."',
												date_avertissement='".strftime("%Y-%m-%d %H:%M:%S")."';";
						//$msg.="$sql<br />";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$insert) {
							$msg.="Erreur lors de l'enregistrement de ".$mod_disc_terme_avertissement_fin_periode.".";
							$nb_err++;
						}
					}
				}
			}

			if($nb_err==0) {
				$msg.="Enregistrement effectué.<br />";
				if(acces("/mod_discipline/imprimer_bilan_periode.php", $_SESSION['statut'])) {
					$tmp_tab_clas=get_class_periode_from_ele_login($login_ele);
					if(isset($tmp_tab_clas['periode'][$periode]['id_classe'])) {
						$current_id_classe=$tmp_tab_clas['periode'][$periode]['id_classe'];
						$msg.="<a href='../mod_discipline/imprimer_bilan_periode.php?id_classe[0]=$current_id_classe&periode[0]=$periode&eleve[0]=$current_id_classe|$periode|$login_ele' target='_blank'>Imprimer ".$prefixe_mod_disc_terme_avertissement_fin_periode_le.$mod_disc_terme_avertissement_fin_periode."</a><br />";
					}
				}
			}

			if($mode_js=="y") {
				if($nb_err==0) {
					//echo "liste_avertissements_fin_periode($login_ele, $periode)<br />";
					echo liste_avertissements_fin_periode($login_ele, $periode);
				}
				else {
					echo "<span style='color:red'>Erreur</span>";
				}
				die();
			}
		}
		else {
			if($mode_js=="y") {
				echo "<span style='color:red'>Période close</span>";
				die();
			}
			else {
				$msg.="La période $periode est close en saisie pour ".get_nom_prenom_eleve($login_ele)."<br />";
			}
		}
	}

	$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
	if($mode_js=="n") {
		//**************** EN-TETE *****************
		$titre_page = "Saisie $mod_disc_terme_avertissement_fin_periode";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************

		//debug_var();

		if($lien_refermer=="y") {
			echo "<p class=bold><a href=\"#\" onclick=\"confirm_close (this, change, '$themessage');\">Refermer la page</a></p>\n";
		}
		else {
			echo "<p class='bold'><a href=\"index.php\" onclick=\"confirm_abandon (this, change, '$themessage');\" title=\"Retour à la page d'accueil du module Discipline\">Retour</a>\n";

			if((isset($periode))&&(isset($login_ele))) {
				echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a> | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir une autre période</a>";
			}

			if((isset($periode))&&(isset($id_classe))) {
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode=$periode'>Choisir un autre élève</a>";
			}
			echo "</p>";
		}
	}

	if((isset($periode))&&(isset($login_ele))) {

		//$tab_avertissement_fin_periode=get_tab_avertissement($login_ele, $periode);

		$lien_suppl="";
		if(acces("/eleves/visu_eleve.php", $_SESSION['statut'])) {
			$lien_suppl="\n"."<div style='float:right; width:16px; margin:3px;'><a href='../eleves/visu_eleve.php?ele_login=".$login_ele."' onclick=\"return confirm_abandon(this, change, '$themessage');\" title=\"Accès aux onglets élève\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets élève' /></a></div>";
		}

		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form_saisie_avt'>
	<fieldset class='fieldset_opacite50'>".$lien_suppl."
		<p class='bold'>Saisie d'$mod_disc_terme_avertissement_fin_periode pour ".get_nom_prenom_eleve($login_ele)." en période $periode&nbsp;:</p>
		".add_token_field()."
		<input type='hidden' name='saisie_avertissement_fin_periode' value='y' />
		<input type='hidden' name='periode' value='$periode' />
		".(isset($id_classe) ? "		<input type='hidden' name='id_classe' value='$id_classe' />" : "")."
		<input type='hidden' name='login_ele' value=\"$login_ele\" />
		<input type='hidden' name='lien_refermer' value=\"$lien_refermer\" />
		".champs_checkbox_avertissements_fin_periode($login_ele, $periode)."
		<input type='submit' value='Enregistrer' />
	</fieldset>
</form>";
	}

	echo "
<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_close(theLink, thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			self.close();
			return false;
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				self.close();
				return false;
			}
			else{
				return false;
			}
		}
	}

	".js_checkbox_change_style('checkbox_change', 'texte_', 'n')."
</script>";

	if($mode_js=="n") {
		require("../lib/footer.inc.php");
	}
}
else {

	$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
	//**************** EN-TETE *****************
	$titre_page = "Saisie $mod_disc_terme_avertissement_fin_periode";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	echo "<p class='bold'><a href=\"index.php\" onclick=\"confirm_abandon (this, change, '$themessage');\" title=\"Retour à la page d'accueil du module Discipline\">Retour</a>\n";

	if(!acces_saisie_avertissement_fin_periode("")) {
		echo "</p>

<p style='color:red'>Vous n'avez pas accès à la saisie ".$prefixe_mod_disc_terme_avertissement_fin_periode_de.$mod_disc_terme_avertissement_fin_periode.".</p>";

		require("../lib/footer.inc.php");
		die();
	}

	// Le login élève est choisi.
	if(isset($login_ele)) {
		$tab_classes_ele=get_class_periode_from_ele_login($login_ele);

		echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>";
		echo "</p>

		<p class='bold'>Saisie d'".$mod_disc_terme_avertissement_fin_periode." pour ".get_nom_prenom_eleve($login_ele)."</p>

		<p style='margin-left:4em; text-indent:-2em;'>Choix de la période&nbsp;:<br />";

		foreach($tab_classes_ele['periode'] as $current_num_per => $current_tab_classe) {
			$id_classe=$current_tab_classe['id_classe'];
			include("../lib/periodes.inc.php");

			if($ver_periode[$current_num_per]!="O") {
				echo "<a href='".$_SERVER['PHP_SELF']."?login_ele=$login_ele&amp;id_classe=".$current_tab_classe['id_classe']."&amp;periode=$current_num_per'>".$current_tab_classe['classe']."&nbsp;: ".$nom_periode[$current_num_per]."</a><br />";
			}
			else {
				echo $current_tab_classe['classe']."&nbsp;: ".$nom_periode[$current_num_per]." (<em style='color:".$couleur_verrouillage_periode['O']."'>période close</em>)<br />";
			}
		}

		require("../lib/footer.inc.php");
		die();
	}

	//===============================
	// Choix de la classe

	if(!isset($id_classe)) {
		echo "</p>";

		if($_SESSION['statut']=='administrateur') {
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
		}
		elseif($_SESSION['statut']=='secours') {
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
		}
		elseif($_SESSION['statut']=='scolarite') {
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
		}
		elseif($_SESSION['statut']=='professeur') {
			$sql="SELECT DISTINCT jec.id_classe AS id, c.classe FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c WHERE jep.professeur='".$_SESSION['login']."' AND jep.login=jec.login AND jec.id_classe=c.id ORDER BY c.classe;";
		}
		elseif($_SESSION['statut']=='cpe') {
			$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
				p.id_classe = c.id AND
				jec.id_classe=c.id AND
				jec.periode=p.num_periode AND
				jecpe.e_login=jec.login AND
				jecpe.cpe_login='".$_SESSION['login']."'
				ORDER BY classe";
		}
		else {
			echo "
<p style='color:red'>Vous n'avez pas accès à la saisie d'".$mod_disc_terme_avertissement_fin_periode.".</p>";
			require("../lib/footer.inc.php");
			die();
		}

		//echo "$sql<br />";
		//$tab=array();
		$txt_classe=array();
		$lien_classe=array();
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if($res->num_rows > 0) {
			while($lig=$res->fetch_object()) {
				//$tab['id_classe'][]=$lig->id_classe;
				//$tab['classe'][]=$lig->classe;
				$txt_classe[]=$lig->classe;
				$lien_classe[]=$_SERVER['PHP_SELF']."?id_classe=".$lig->id;
			}
			$res->close();
		}

		echo "
<p>Sélectionnez la classe : </p>
<blockquote>\n";

		if(count($txt_classe)>0) {
			tab_liste($txt_classe,$lien_classe,3);
		}
		else {
			echo "<p style='color:red'>Vous n'êtes associé à aucun élève.</p>\n";
		}

		require("../lib/footer.inc.php");
		die();
	}

	//===============================
	// Choix de la période

	if(!isset($periode)) {
		/*
		if(isset($login_ele)) {
			$tab_classes_ele=get_class_periode_from_ele_login($login_ele);

			echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>";
			echo "</p>

			<p class='bold'>Saisie d'".$mod_disc_terme_avertissement_fin_periode." pour ".get_nom_prenom_eleve($login_ele)."</p>

			<p style='margin-left:4em; text-indent:-2em;'>Choix de la période&nbsp;:<br />";

			foreach($tab_classes_ele['periode'] as $current_num_per => $current_tab_classe) {
				$id_classe=$current_tab_classe['id_classe'];
				include("../lib/periodes.inc.php");

				if($ver_periode[$current_num_per]!="O") {
					echo "<a href='".$_SERVER['PHP_SELF']."?login_ele=$login_ele&amp;id_classe=".$current_tab_classe['id_classe']."&amp;periode=$current_num_per'>".$current_tab_classe['classe']."&nbsp;: ".$nom_periode[$current_num_per]."</a><br />";
				}
				else {
					echo $current_tab_classe['classe']."&nbsp;: ".$nom_periode[$current_num_per]." (<em style='color:".$couleur_verrouillage_periode['O']."'>période close</em>)<br />";
				}
			}
		}
		else {
		*/
			echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>";

			$nom_classe=get_nom_classe($id_classe);

			echo "</p>

			<p class='bold'>Saisie d'".$mod_disc_terme_avertissement_fin_periode." pour la classe de ".$nom_classe;

			echo "</p>
			<p style='margin-left:4em; text-indent:-2em;'>Choix de la période&nbsp;:<br />";

			include("../lib/periodes.inc.php");

			$i = "1";
			while ($i < $nb_periode) {
				if($ver_periode[$i]!="O") {
					echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode=$i'>".$nom_periode[$i]."</a><br />";
				}
				else {
					echo $nom_periode[$i]." (<em style='color:".$couleur_verrouillage_periode['O']."'>période close</em>)<br />";
				}
				$i++;
			}
		//}

		require("../lib/footer.inc.php");
		die();
	}

	//===============================
	// Choix de l'élève

	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a> | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir une autre période</a></p>
	
	<p class='bold'>Classe de ".get_nom_classe($id_classe)." en période $periode</p>
	<p>Choix de l'élève&nbsp;:<br />";

	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND jec.periode='$periode' ORDER BY e.nom, e.prenom;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucun élève n'a été trouvé.</p>\n";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode=$periode&amp;login_ele=".$lig->login."' title=\"Saisir un ".$mod_disc_terme_avertissement_fin_periode." pour cet élève en période $periode\">".$lig->nom." ".$lig->prenom."</a><br />\n";
		}
	}

	require("../lib/footer.inc.php");
}
?>
