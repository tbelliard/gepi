<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);
if((!isset($login_eleve))||(trim($login_eleve)=="")) {
	header("Location: ../accueil.php?msg=Elève non choisi.");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
if((!isset($id_classe))||(!preg_match("/^[0-9]{1,}$/", $id_classe))) {
	$id_classe=get_derniere_classe_from_ele_login($login_eleve);
	//echo "\$id_classe=$id_classe<br />";
	//die();
}

if((!isset($id_classe))||(!preg_match("/^[0-9]{1,}$/", $id_classe))) {
	header("Location: ../accueil.php?msg=Classe non choisie.");
	die();
}

// Seuls les comptes Administrateur et Scolarité ont accès
if($_SESSION['statut']=="scolarite") {
	// Tester si le compte scolarité a accès à cette classe...
	// Si ce n'est pas le cas -> intrusion...

	$sql="SELECT 1=1 FROM j_scol_classes jsc WHERE jsc.id_classe='$id_classe' AND jsc.login='".$_SESSION['login']."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if ($test == "0") {
		tentative_intrusion("2", "Tentative d'accès par un compte scolarité à une classe à laquelle il n'est pas associé.");
		echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas référent !";
		require ("../lib/footer.inc.php");
		die();
	}
}

include "../lib/periodes.inc.php";

$grp_edt=isset($_POST['grp_edt']) ? $_POST['grp_edt'] : (isset($_GET['grp_edt']) ? $_GET['grp_edt'] : "n");

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
	if (isset($is_posted)) {
		check_token();
		$msg = '';
		$j = 1;
		while ($j < $nb_periode) {
			$sql="SELECT DISTINCT g.id, g.name FROM groupes g, j_groupes_classes jgc WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."') ORDER BY jgc.priorite, g.name";
			//echo "$sql<br />";
			$call_group = mysqli_query($GLOBALS["mysqli"], $sql);
			$nombre_ligne = mysqli_num_rows($call_group);
			$i=0;
			while ($i < $nombre_ligne) {
				$lig_grp=mysqli_fetch_object($call_group);
				$id_groupe = $lig_grp->id;
				$nom_groupe = $lig_grp->name;
				$id_group[$j] = $id_groupe."_".$j;
				$sql="SELECT 1=1 FROM j_eleves_groupes WHERE (" .
						"id_groupe = '" . $id_groupe . "' and " .
						"login = '" . $login_eleve . "' and " .
						"periode = '" . $j . "')";
				//echo "$sql<br />";
				$test_query = mysqli_query($GLOBALS["mysqli"], $sql);
				$test = mysqli_num_rows($test_query);
				if (isset($_POST[$id_group[$j]])) {
					if ($test == 0) {
						$sql="INSERT INTO j_eleves_groupes SET id_groupe = '" . $id_groupe . "', login = '" . $login_eleve . "', periode = '" . $j ."'";
						//echo "$sql<br />";
						$req = mysqli_query($GLOBALS["mysqli"], $sql);
					}
				} else {
					$sql="SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')";
					//echo "$sql<br />";
					$test1 = mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_test1 = mysqli_num_rows($test1);

					$sql="SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$login_eleve."' and periode = '$j')";
					//echo "$sql<br />";
					$test2 = mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_test2 = mysqli_num_rows($test2);

					if (($nb_test1 != 0) or ($nb_test2 != 0)) {
						$msg = $msg."--> Impossible de supprimer cette option pour l'élève $login_eleve car des moyennes ou appréciations ont déjà été rentrées pour le groupe $nom_groupe pour la période $j ! Commencez par supprimer ces données !<br />";
					} else {
						if ($test != "0") {
							$sql="DELETE FROM j_eleves_groupes WHERE (login='".$login_eleve."' and id_groupe='".$id_groupe."' and periode = '".$j."')";
							//echo "$sql<br />";
							$req = mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
				}
				$i++;
			}
			$j++;
		}

		// Récupérer les modalités associées à l'élève pour les différents groupes
		$tab_modalites_eleve=array();
		$sql="SELECT * FROM j_groupes_eleves_modalites WHERE login='".$login_eleve."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$tab_modalites_eleve[$lig->id_groupe]=$lig->code_modalite_elect;
		}
		/*
		echo "<pre>";
		print_r($tab_modalites_eleve);
		echo "</pre>";
		*/
		$nb_insert_modalite=0;
		$nb_update_modalite=0;
		$nb_suppr_modalite=0;
		$modalite=isset($_POST['modalite']) ? $_POST['modalite'] : array();
		foreach($modalite as $current_id_groupe => $current_code_modalite_elect) {
			// Comparer: Si changement de modalité...
			if((!isset($tab_modalites_eleve[$current_id_groupe]))&&($current_code_modalite_elect!="")) {
				$sql="INSERT INTO j_groupes_eleves_modalites SET login='".$login_eleve."', id_groupe='".$current_id_groupe."', code_modalite_elect='".$current_code_modalite_elect."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement de la modalité pour l'enseignement n°$current_id_groupe.<br />";
				}
				else {
					$nb_insert_modalite++;
				}
			}
			elseif((isset($tab_modalites_eleve[$current_id_groupe]))&&($current_code_modalite_elect=="")) {
				$sql="DELETE FROM j_groupes_eleves_modalites WHERE login='".$login_eleve."' AND id_groupe='".$current_id_groupe."';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression de la modalité pour l'enseignement n°$current_id_groupe.<br />";
				}
				else {
					$nb_suppr_modalite++;
				}
			}
			elseif((isset($tab_modalites_eleve[$current_id_groupe]))&&($tab_modalites_eleve[$current_id_groupe]!=$current_code_modalite_elect)) {
				$sql="UPDATE j_groupes_eleves_modalites SET code_modalite_elect='".$current_code_modalite_elect."' WHERE  login='".$login_eleve."' AND id_groupe='".$current_id_groupe."';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="Erreur lors de la mise à jour de la modalité pour l'enseignement n°$current_id_groupe.<br />";
				}
				else {
					$nb_update_modalite++;
				}
			}
		}
		if($nb_insert_modalite>0) {
			$msg.=$nb_insert_modalite." modalité(s) enregistrée(s).<br />";
		}
		if($nb_update_modalite>0) {
			$msg.=$nb_update_modalite." modalité(s) mise(s) à jour.<br />";
		}
		if($nb_suppr_modalite>0) {
			$msg.=$nb_suppr_modalite." modalité(s) supprimée(s).<br />";
		}

		// On vide les signalements par un prof lors de l'enregistrement
		$sql="DELETE FROM j_signalement WHERE nature='erreur_affect' AND login='".$login_eleve."';";
		//echo "$sql<br />";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);

		//$affiche_message = 'yes';
		if($msg=='') {$msg= "Les modifications ont été enregistrées !";}
	}
	//$message_enregistrement = "Les modifications ont été enregistrées !";
}


// =================================
// AJOUT: boireaus
//$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec, eleves e
$sql="SELECT DISTINCT jec.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e
						WHERE jec.login=e.login AND
							jec.id_classe='$id_classe'
						ORDER BY e.nom,e.prenom";
//echo "$sql<br />";
//echo "\$login_eleve=$login_eleve<br />";
$res_ele_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
$chaine_options_login_eleves="";
$cpt_eleve=0;
$num_eleve=-1;
if(mysqli_num_rows($res_ele_tmp)>0){
	$login_eleve_prec=0;
	$login_eleve_suiv=0;
	$temoin_tmp=0;
	while($lig_ele_tmp=mysqli_fetch_object($res_ele_tmp)){
		if($lig_ele_tmp->login==$login_eleve){
			$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login' selected='true'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";

			$num_eleve=$cpt_eleve;

			$temoin_tmp=1;
			if($lig_ele_tmp=mysqli_fetch_object($res_ele_tmp)){
				$login_eleve_suiv=$lig_ele_tmp->login;
				$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
			}
			else{
				$login_eleve_suiv=0;
			}
		}
		else{
			$chaine_options_login_eleves.="<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
		}

		if($temoin_tmp==0){
			$login_eleve_prec=$lig_ele_tmp->login;
		}
		$cpt_eleve++;
	}
}
// =================================


if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
	$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
}
//**************** EN-TETE **************************************
$titre_page = "Gestion des classes | Gestion des matières par élève";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//=============================
// MODIF: boireaus
//echo "<p class=bold>|<a href=\"classes_const.php?id_classe=".$id_classe."\">Retour</a>|";

$call_data_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE (login = '$login_eleve')");
if(mysqli_num_rows($call_data_eleves)==0) {
	echo "<p style='color:red'>Élève inconnu</p>";
	require("../lib/footer.inc.php");
	die();
}
$tab_info_ele=mysqli_fetch_assoc($call_data_eleves);
$nom_eleve=$tab_info_ele['nom'];
$prenom_eleve=$tab_info_ele['prenom'];
$ine_eleve=$tab_info_ele['no_gep'];

$temoin_noms_edt_groupes="n";
$tab_grp_edt=array();
$sql="SELECT * FROM edt_corresp2 ec, j_groupes_classes jgc WHERE jgc.id_groupe=ec.id_groupe AND jgc.id_classe='".$id_classe."';";
$res_grp_edt=mysqli_query($GLOBALS['mysqli'], $sql);
if(mysqli_num_rows($res_grp_edt)>0) {
	$temoin_noms_edt_groupes="y";
	if($grp_edt=="y") {
		while($lig_edt_grp=mysqli_fetch_assoc($res_grp_edt)) {
			$tab_grp_edt[$lig_edt_grp["id_groupe"]]=$lig_edt_grp;
			$tmp_tab=explode(",", $lig_edt_grp["nom_groupe_edt"]);
			for($loop=0;$loop<count($tmp_tab);$loop++) {
				$tmp_nom=trim($tmp_tab[$loop]);
				if($tmp_nom!="") {
					$tab_grp_edt[$lig_edt_grp["id_groupe"]]["nom_groupe_edt_2"][]=$tmp_nom;
				}
			}
		}
	}
}

if(!isset($quitter_la_page)) {
	echo "<form action='eleve_options.php' name='form1' method='post'>\n";
	echo "<input type='hidden' name='grp_edt' value='$grp_edt' />\n";

	echo "<p class=bold><a href=\"classes_const.php?id_classe=".$id_classe."\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

	if("$login_eleve_prec"!="0"){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_eleve=$login_eleve_prec".(($grp_edt=="y") ? "&grp_edt=y" : "")."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Elève précédent</a>";
	}


	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_eleve(thechange, themessage)
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
				document.getElementById('login_eleve').selectedIndex=$num_eleve;
			}
		}
	}
</script>\n";


	echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
	//echo " | <select name='login_eleve' onchange='document.form1.submit()'>\n";
	echo " | <select name='login_eleve' id='login_eleve' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
	echo $chaine_options_login_eleves;
	echo "</select>\n";

	if("$login_eleve_suiv"!="0"){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_eleve=$login_eleve_suiv".(($grp_edt=="y") ? "&grp_edt=y" : "")."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Elève suivant</a>";
	}

	if($temoin_noms_edt_groupes=="y") {
		if($grp_edt=="y") {
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_eleve=$login_eleve&grp_edt=n' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Afficher la page sans les noms de regroupements EDT.\">Sans noms de groupes EDT</a>";
		}
		else {
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;login_eleve=$login_eleve&grp_edt=y' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Afficher la page avec les noms de regroupements EDT.\">Afficher noms de groupes EDT</a>";
		}
	}

	echo " | <a href='export_ele_opt.php?id_classe[0]=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Exporter les options suivies par les élèves de ".get_nom_classe($id_classe)."</a>";

	if(acces("/groupes/maj_inscript_ele_d_apres_edt.php", $_SESSION['statut'])) {
		echo " | <a href='../groupes/maj_inscript_ele_d_apres_edt.php' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Mettre à jour les inscriptions dans les groupes d'après un export XML Eleves d'EDT.\">Màj XML STS</a>";
	}

	echo "</p>\n";
	echo "</form>\n";
}
else{
	// Cette page a été ouverte en target='blank' depuis une autre page (par exemple /eleves/modify_eleve.php)
	// Après modification éventuelle, il faut quitter cette page.
	//echo "<p class=bold><a href=\"#\" onclick=\"return confirm_abandon (this, change, '$themessage');\">Refermer la page</a></p>\n";
	//echo "<p class=bold><a href=\"#\" onclick=\"if(return confirm_abandon (this, change, '$themessage')){self.close()};\">Refermer la page</a></p>\n";
	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
		echo "<p class=bold><a href=\"#\" onclick=\"confirm_close (this, change, '$themessage');\">Refermer la page</a></p>\n";
	}
	else{
		echo "<p class=bold><a href=\"#\" onclick=\"self.close();\">Refermer la page</a></p>\n";
	}

	echo "<script type='text/javascript'>
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
</script>\n";

}

//debug_var();

//=============================

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
	echo "<form action='eleve_options.php' name='form2' method=post>\n";
	echo "<input type='hidden' name='grp_edt' value='$grp_edt' />\n";

	echo add_token_field();

	if(isset($quitter_la_page)){
		// Cette page a été ouverte en target='blank' depuis une autre page (par exemple /eleves/modify_eleve.php)
		// Après modification éventuelle, il faut quitter cette page.
		echo "<input type='hidden' name='quitter_la_page' value='y' />\n";
	}
}

$classe = get_nom_classe($id_classe);

// A VOIR: Pouvoir restreindre les modalités autorisées pour telle ou telle matière.
$tab_modalites=get_tab_modalites_election();
/*
echo "<pre>";
print_r($tab_modalites);
echo "</pre>";
*/

echo "<h3>";
if(acces("/eleves/modify_eleve.php", $_SESSION['statut'])) {
	echo "<a href='../eleves/modify_eleve.php?eleve_login=".$login_eleve."' title=\"Voir la fiche élève\"";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">".$nom_eleve." ".$prenom_eleve."</a>";
}
else {
	echo $nom_eleve." ".$prenom_eleve;
}
if(acces("/eleves/visu_eleve.php", $_SESSION['statut'])) {
	echo " <a href='../eleves/visu_eleve.php?ele_login=".$login_eleve."' title=\"Voir les onglets élève\"";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo "><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a>";
}
echo " - Classe : ";
if(acces("/classes/classes_const.php", $_SESSION['statut'])) {
	echo "<a href='../classes/classes_const.php?id_classe=$id_classe' title=\"Voir la composition de la classe (élèves)\"";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">".$classe."</a>";
}
else {
	echo $classe;
}
echo "</h3>\n";

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
	//echo "<p>Pour valider les modifications, cliquez sur le bouton qui apparait en bas de la page.</p>\n";
	echo "<p>Pour valider les modifications, cliquez sur le bouton.</p>\n";

	echo "<p align='center'><input type='submit' value='Enregistrer les modifications' /></p>\n";
}

// J'appelle les différents groupes existants pour la classe de l'élève

//$call_group = mysql_query("SELECT DISTINCT g.id, g.name FROM groupes g, j_groupes_classes jgc WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."') ORDER BY jgc.priorite, g.name");
$call_group = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT g.id, g.name,g.description, jgm.id_matiere FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (g.id = jgc.id_groupe and jgc.id_classe = '" . $id_classe ."' AND jgm.id_groupe=g.id) ORDER BY jgc.priorite, g.name");
$nombre_ligne = mysqli_num_rows($call_group);

$tab_sig=array();
$sql="SELECT * FROM j_signalement WHERE nature='erreur_affect' AND login='$login_eleve';";
//echo "$sql<br />";
$res_sig=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_sig)>0) {
	while($lig_sig=mysqli_fetch_object($res_sig)) {
		$tab_sig[$lig_sig->periode][$lig_sig->id_groupe]=my_ereg_replace("_"," ",$lig_sig->valeur)." selon ".affiche_utilisateur($lig_sig->declarant,$id_classe);
		//echo my_ereg_replace("_"," ",$lig_sig->valeur)." selon ".affiche_utilisateur($lig_sig->declarant,$id_classe)."<br />";
		//echo "\$tab_sig[$lig_sig->periode][$lig_sig->id_groupe]=".$tab_sig[$lig_sig->periode][$lig_sig->id_groupe]."<br />";
	}
}


if($grp_edt=="y") {
	$tab_grp_edt_eleve=array();
	if($ine_eleve!="") {
		$sql="SELECT * FROM edt_eleves_lignes WHERE n_national='".$ine_eleve."';";
		$res_grp_edt=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res_grp_edt)>0) {
			while($lig_edt_grp=mysqli_fetch_object($res_grp_edt)) {
				// Normalement, on ne fait qu'un tour dans la boucle.
				echo "<p>Lors du dernier upload EXP_Eleve.xml d'EDT, cet élève était inscrit dans les regroupements EDT suivants&nbsp;: <span style='color:green'>".preg_replace("/, /",",<br />",htmlentities($lig_edt_grp->classe))."</span></p>";
				$tmp_tab=explode(",", $lig_edt_grp->classe);
				for($loop=0;$loop<count($tmp_tab);$loop++) {
					$tmp_nom=trim($tmp_tab[$loop]);
					if($tmp_nom!="") {
						$tab_grp_edt_eleve[]=$tmp_nom;
					}
				}
			}
		}
	}
}
//=========================
echo "<table border='1' cellpadding='5' cellspacing='0' class='boireaus'>\n";
echo "<tr align='center'>\n";
echo "<th>Enseignement</th>\n";
echo "<th style='font-size:x-small;'>Matière</th>\n";
echo "<th>Classes</th>\n";
//=========================
$j = 1;
$chaine_coche="";
$chaine_decoche="";
while ($j < $nb_periode) {
	//=========================
	echo "<th><b>".$nom_periode[$j]."</b>";
	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
		echo "<br />\n";
		echo "<a href='javascript:modif_case($j,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		echo "<a href='javascript:modif_case($j,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

		if($j>1) {
			echo "/<a href=\"javascript:copieEnseignementsPeriode1(".$j.")\"><img src='../images/icons/copy-16.png' width='16' height='16' alt='Copier les affectations de la première période' title='Copier les affectations de la première période' /></a>";
		}

	}
	echo "</th>\n";

	$chaine_coche.="modif_case($j,\"col\",true);";
	$chaine_decoche.="modif_case($j,\"col\",false);";

	//=========================
	$j++;
}
//echo "<th>&nbsp;</th>\n";

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
	echo "<th>\n";
	echo "<a href='javascript:$chaine_coche'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
	echo "<a href='javascript:$chaine_decoche'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
	echo "</th>\n";
}
echo "<th>Modalités</th>\n";

if($grp_edt=="y") {
	echo "<th>Nom EDT</th>
<th>Groupes EDT élève</th>";
}
echo "</tr>\n";

$acces_edit_group=acces("/groupes/edit_group.php", $_SESSION['statut']);
$acces_edit_eleves=acces("/groupes/edit_eleves.php", $_SESSION['statut']);
$nb_erreurs=0;
$i=0;
$alt=1;
while ($i < $nombre_ligne) {
	$lig_grp=mysqli_fetch_object($call_group);

	$id_groupe = $lig_grp->id;
	$nom_groupe = $lig_grp->name;
	$description_groupe = htmlspecialchars($lig_grp->description);
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover' id='tr_$i'>\n";
	echo "<td>";

	$sql="SELECT u.nom,u.prenom FROM j_groupes_professeurs jgp, utilisateurs u WHERE
			jgp.login=u.login AND
			jgp.id_groupe='".$id_groupe."'
			ORDER BY u.nom,u.prenom";
	$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
	$texte_alternatif="Pas de prof???";
	if(mysqli_num_rows($res_prof)>0){
		$texte_alternatif="";
		while($ligne=mysqli_fetch_object($res_prof)){
			$texte_alternatif.=", ".casse_mot($ligne->prenom,'majf2')." ".my_strtoupper($ligne->nom);
		}
		$texte_alternatif=mb_substr($texte_alternatif,2);
	}

	$sql="SELECT DISTINCT c.classe FROM classes c, j_groupes_classes jgc WHERE jgc.id_groupe='$id_groupe' AND c.id=jgc.id_classe ORDER BY c.classe;";
	$res_clas_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	$liste_classes_du_groupe="";
	while($lig_classe=mysqli_fetch_object($res_clas_grp)) {
		$liste_classes_du_groupe.=", ".$lig_classe->classe;
	}
	if($liste_classes_du_groupe!='') {$liste_classes_du_groupe=mb_substr($liste_classes_du_groupe,2);}

	$texte_alternatif.=" (".$liste_classes_du_groupe.")";

	if($acces_edit_group) {
		echo "<a href='../groupes/edit_group.php?id_groupe=$id_groupe&amp;id_classe=$id_classe&amp;mode=groupe' title=\"$texte_alternatif : Éditer cet enseignement.\">";
		echo $nom_groupe;
		echo "<br /><span style='font-size:xx-small;'>$description_groupe</span>";
		echo "</a>";
	}
	elseif(($acces_edit_eleves)&&($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes'))) {
		echo "<a href='../groupes/edit_eleves.php?id_groupe=$id_groupe&amp;id_classe=$id_classe' title=\"$texte_alternatif : Consulter/modifier la liste des élèves cet enseignement.\">";
		echo $nom_groupe;
		echo "<br /><span style='font-size:xx-small;'>$description_groupe</span>";
		echo "</a>";
	}
	else {
		echo $nom_groupe;
		echo "<br /><span style='font-size:xx-small;'>$description_groupe</span>";
	}
	echo "</td>\n";
	echo "<td style='font-size:x-small;'>".$lig_grp->id_matiere."</td>";
	echo "<td>".$liste_classes_du_groupe."</td>";
	$j = 1;
	while ($j < $nb_periode) {
		$tmp_ele_grp=get_eleves_from_groupe($id_groupe,$j);
		if(isset($tmp_ele_grp['users'])) {
			$eff_grp=count($tmp_ele_grp['users']);
		}
		else {
			$eff_grp=0;
		}

		$test=mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM j_eleves_groupes WHERE (" .
				"id_groupe = '" . $id_groupe . "' and " .
				"login = '" . $login_eleve . "' and " .
				"periode = '" . $j . "')");

		//$sql="SELECT * FROM j_eleves_classes WHERE login='$login_eleve' AND periode='$j'";
		$sql="SELECT * FROM j_eleves_classes WHERE login='$login_eleve' AND periode='$j' AND id_classe='$id_classe'";
		// CA NE VA PAS... SUR LES GROUPES A REGROUPEMENT, IL FAUT PRENDRE DES PRECAUTIONS...
		$res_test_class_per=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_test_class_per)==0){
			if (mysqli_num_rows($test) == "0") {
				echo "<td>&nbsp;</td>\n";
			}
			else{
				$sql="SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='$id_groupe'";
				$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
				$temoin="";
				//$liste_classes_du_groupe="";
				while($lig_clas=mysqli_fetch_object($res_grp)){
					/*
					$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
					$res_tmp=mysql_query($sql);
					$lig_tmp=mysql_fetch_object($res_tmp);
					$liste_classes_du_groupe.=", ";
					$liste_classes_du_groupe.=$lig_tmp->classe;
					*/
					$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$lig_clas->id_classe' AND login='$login_eleve' AND periode='$j'";
					$res_test_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_test_ele)==1){
						
						$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
						$res_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
						$lig_tmp=mysqli_fetch_object($res_tmp);
						
						$clas_tmp=$lig_tmp->classe;

						$temoin=$clas_tmp;
					}
				}
				//if($liste_classes_du_groupe!='') {$liste_classes_du_groupe=mb_substr($liste_classes_du_groupe,2);}

				echo "<td style='text-align:center'>";
				if($temoin!="") {
					echo $temoin;
					if(($_SESSION['statut']=="administrateur")||
					(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
						echo "<input type='hidden' name='".$id_groupe."_".$j."' value='y' />";
					}
					else {
						echo "<img src='../images/enabled.png' width='15' height='15' alt='Inscrit' />";
					}
				}
				else {
					if(($_SESSION['statut']=="administrateur")||
					(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
						$msg_erreur="Cette case est validée et ne devrait pas l être. Validez le formulaire pour corriger.";
						echo "<a href='#' alt='$msg_erreur' title='$msg_erreur'><font color='red'>ERREUR</font></a>";
					}
					else{
						$msg_erreur="Cette case est validée et ne devrait pas l être. Contactez l administrateur pour corriger.";
						echo "<a href='#' alt='$msg_erreur' title='$msg_erreur'><font color='red'>ERREUR</font></a>";
					}
					$nb_erreurs++;
				}


				// Test sur la présence de notes dans cn ou de notes/app sur bulletin
				if (!test_before_eleve_removal($login_eleve, $id_groupe, $j)) {
					// AJOUTER UN LIEN SI DROIT ACCES acces_impression_bulletin($login_eleve, $id_classe="")
					echo "<img id='img_bull_non_vide_".$i."_".$j."' src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
				}
	
				$sql="SELECT DISTINCT id_devoir FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '".$login_eleve."' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '".$id_groupe."' AND ccn.periode = '".$j."')";
				$test_cn=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_notes_cn=mysqli_num_rows($test_cn);
				if($nb_notes_cn>0) {
					echo "<img id='img_cn_non_vide_".$i."_".$j."' src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Carnet de notes non vide: $nb_notes_cn notes' />";
					//echo "$sql<br />";
				}

				//echo "A".$tab_sig[$j][$id_groupe]."<br />";
				if((isset($tab_sig[$j]))&&(isset($tab_sig[$j][$id_groupe]))) {
					$info_erreur=$tab_sig[$j][$id_groupe];
					echo "<img id='img_erreur_affect_".$i."_".$j."' src='../images/icons/flag2.gif' width='17' height='18' title='".$info_erreur."' alt='".$info_erreur."' />";
				}

				echo "</td>\n";
			}
		}
		else {

			/*
			// Un autre test à faire:
			// Si l'élève est resté dans le groupe alors qu'il n'est plus dans cette classe pour la période
			$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$j' AND login='$login_eleve'";
			*/

			echo "<td style='text-align:center' id='td_case".$i."_".$j."'>\n";
			if(($_SESSION['statut']=="administrateur")||
			(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
				echo "<input type='checkbox' id='case".$i."_".$j."' name='".$id_groupe."_".$j."' onchange='changement(); colore_td_eleve_options($i);' value='y' ";
				if (mysqli_num_rows($test)>0) {
					echo "checked ";
				}
				echo "/>\n";
			}
			else {
				echo "<input type='checkbox' id='case".$i."_".$j."' name='".$id_groupe."_".$j."' onchange='changement(); colore_td_eleve_options($i);' value='y' style='display:none; '";
				if (mysqli_num_rows($test)==0) {
					echo "/>\n";
					echo "&nbsp;\n";
				}
				else {
					echo "checked ";
					echo "/>\n";
					echo "<img src='../images/enabled.png' width='15' height='15' alt='Inscrit' />\n";
				}
			}

			// Test sur la présence de notes dans cn ou de notes/app sur bulletin
			if (!test_before_eleve_removal($login_eleve, $id_groupe, $j)) {
				echo "<img id='img_bull_non_vide_".$i."_".$j."' src='../images/icons/bulletin_16.png' width='16' height='16' title='Bulletin non vide' alt='Bulletin non vide' />";
			}

			$sql="SELECT DISTINCT id_devoir FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '".$login_eleve."' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '".$id_groupe."' AND ccn.periode = '".$j."')";
			$test_cn=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_notes_cn=mysqli_num_rows($test_cn);
			if($nb_notes_cn>0) {
				echo "<img id='img_cn_non_vide_".$i."_".$j."' src='../images/icons/cn_16.png' width='16' height='16' title='Carnet de notes non vide: $nb_notes_cn notes' alt='Carnet de notes non vide: $nb_notes_cn notes' />";
				//echo "$sql<br />";
			}

			if((isset($tab_sig[$j]))&&(isset($tab_sig[$j][$id_groupe]))) {
				$info_erreur=$tab_sig[$j][$id_groupe];
				echo "<img id='img_erreur_affect_".$i."_".$j."' src='../images/icons/flag2.gif' width='17' height='18' title='".$info_erreur."' alt='".$info_erreur."' />";
			}

			echo " <em style='font-size:x-small' title=\"$eff_grp élève(s) sont inscrits dans cet enseignement en période $i.\n\n(effectif enregistré ne tenant pas compte des éventuelles modifications non encore validées dans cette page).\">($eff_grp)</em>";
			echo "</td>\n";
		}
		$j++;
	}
	//=========================

	$current_group=get_group($id_groupe);

	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
		echo "<td>\n";
		echo "<a href='javascript:modif_case($i,\"lig\",true);'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		echo "<a href='javascript:modif_case($i,\"lig\",false);'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
		echo "</td>\n";

		// Modalités
		echo "<td>";
		echo "
		<select name='modalite[$id_groupe]'>
			<option value=''></option>";
		for($loop_m=0;$loop_m<count($tab_modalites);$loop_m++) {
			echo "
			<option value='".$tab_modalites[$loop_m]["code_modalite_elect"]."'";
			if((isset($current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves"]))&&(in_array($login_eleve, $current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves"]))) {
				echo " selected='selected'";
			}
			elseif((!isset($current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves_possibles"]))||(!in_array($login_eleve, $current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves_possibles"]))) {
				echo " disabled style='color:red' title=\"Modalité non associée à l'enseignement dans vos nomenclatures.\"";
			}
			echo ">".$tab_modalites[$loop_m]["libelle_court"];
			// Debug
			//echo " (".$tab_modalites[$loop_m]["code_modalite_elect"].")";
			echo "</option>";
		}
		echo "</select>";
		/*
		// Debug: 20170315
		if(($current_group["id"]=="3825")||
		($current_group["id"]=="3839")) {
			for($loop_m=0;$loop_m<count($tab_modalites);$loop_m++) {
				// Debug
				echo "<br />Modalité ".$tab_modalites[$loop_m]["code_modalite_elect"];
				echo "<pre>";
				//print_r($current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves"]);
				print_r($current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]);
				echo "</pre>";
			}
		}
		*/
		echo "</td>";
	}
	else {
		// Affichage des modalités
		echo "<td>";
		for($loop_m=0;$loop_m<count($tab_modalites);$loop_m++) {
			if((isset($current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves"]))&&(in_array($login_eleve, $current_group["modalites"][$tab_modalites[$loop_m]["code_modalite_elect"]]["eleves"]))) {
				echo $tab_modalites[$loop_m]["libelle_court"];
				break;
			}
		}
		echo "</td>";
	}

	if($grp_edt=="y") {
		echo "
		<td style='font-size:small' title=\"Regroupement EDT associé à ce groupe\">".(isset($tab_grp_edt[$id_groupe]["nom_groupe_edt"]) ? htmlentities(preg_replace("/, /", ",<br />", $tab_grp_edt[$id_groupe]["nom_groupe_edt"])) : "")."</td>
		<td style='font-size:small'>";
		// Parcours des groupes de l'élève 
		for($loop_grp_edt_ele=0;$loop_grp_edt_ele<count($tab_grp_edt_eleve);$loop_grp_edt_ele++) {
			if((isset($tab_grp_edt[$id_groupe]["nom_groupe_edt_2"]))&&(in_array($tab_grp_edt_eleve[$loop_grp_edt_ele], $tab_grp_edt[$id_groupe]["nom_groupe_edt_2"]))) {
				echo "<span title=\"Regroupement correspondant pour l'élève\">".htmlentities($tab_grp_edt_eleve[$loop_grp_edt_ele])."</span>";
				//echo "<br />";
				//echo "<img src='../images/enabled.png' class='icone16' alt='A cocher' title=\"".$tab_grp_edt_eleve[$loop_grp_edt_ele]."\" />";
			}
		}
		echo "</td>";
	}
	//=========================
	echo "</tr>\n";
	$i++;
}

echo "<tr>\n";
echo "<th>\n";
echo "&nbsp;";
echo "</th>\n";
echo "<th>\n";
echo "&nbsp;";
echo "</th>\n";
echo "<th>\n";
echo "&nbsp;";
echo "</th>\n";
$j = 1;
while ($j < $nb_periode) {

	echo "<th>\n";
	echo "<a href='javascript:DecocheColonne_si_bull_et_cn_vide($j)'><img src='../images/icons/wizard.png' width='16' height='16' alt='Décocher les élèves sans note/app sur les bulletin et carnet de notes' title='Décocher les élèves sans note/app sur les bulletin et carnet de notes' /></a>\n";
	echo "</th>\n";

	$j++;
}

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
	echo "<th>\n";
	echo "&nbsp;";
	echo "</th>\n";
}

echo "<th></th>\n";
if($grp_edt=="y") {
	echo "<th></th>\n";
	echo "<th></th>\n";
}
echo "</tr>\n";

echo "</table>\n";


//============================================
// AJOUT: boireaus
echo "<script type='text/javascript' language='javascript'>

	function DecocheColonne_si_bull_et_cn_vide(i) {
		for (var ki=0;ki<$nombre_ligne;ki++) {
			if((document.getElementById('case'+ki+'_'+i))&&(!document.getElementById('img_bull_non_vide_'+ki+'_'+i))&&(!document.getElementById('img_cn_non_vide_'+ki+'_'+i))) {
				document.getElementById('case'+ki+'_'+i).checked = false;
				colore_td_eleve_options(ki);
			}
		}
		changement();
	}

	function copieEnseignementsPeriode1(num_periode) {
		for (var ki=0;ki<$nombre_ligne;ki++) {
			if((document.getElementById('case'+ki+'_1'))&&(document.getElementById('case'+ki+'_'+num_periode))) {
				document.getElementById('case'+ki+'_'+num_periode).checked=document.getElementById('case'+ki+'_1').checked;
			}
		}
	}

	function modif_case(rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$nombre_ligne;k++){
				if(document.getElementById('case'+k+'_'+rang)){
					document.getElementById('case'+k+'_'+rang).checked=statut;

					colore_td_eleve_options(k);
				}
			}
		}
		else{
			for(k=1;k<$nb_periode;k++){
				if(document.getElementById('case'+rang+'_'+k)){
					document.getElementById('case'+rang+'_'+k).checked=statut;
				}
			}
			colore_td_eleve_options(rang);
		}
		changement();
	}

	function colore_td_eleve_options(ligne) {

		if(document.getElementById('tr_'+ligne)) {
			document.getElementById('tr_'+ligne).style.backgroundColor='';
		}

		temoin='n';
		for(i=1;i<".$nb_periode.";i++) {
			if(document.getElementById('case'+ligne+'_'+i)){
				if(document.getElementById('case'+ligne+'_'+i).checked) {
					// Au moins une période cochée
					temoin='y';
				}
			}
		}

		if(temoin=='n') {
			if(document.getElementById('tr_'+ligne)) {
				document.getElementById('tr_'+ligne).style.backgroundColor='grey';
			}
		}

		for(i=1;i<".$nb_periode.";i++) {
			if(document.getElementById('case'+ligne+'_'+i).checked) {
				document.getElementById('td_case'+ligne+'_'+i).style.backgroundColor='';
			}
			else {
				document.getElementById('td_case'+ligne+'_'+i).style.backgroundColor='grey';
			}
		}
	}

	for (var ki=0;ki<$nombre_ligne;ki++) {
		colore_td_eleve_options(ki);
	}

</script>\n";

if($nb_erreurs>0){
	echo "<p style='color:red;'>Cet élève est affecté dans des groupes sur des périodes pour lesquelles il n'est pas dans la classe.<br />";
	if(($_SESSION['statut']=="administrateur")||
	(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
		echo "Pour supprimer l'élève de ces groupes, validez le présent formulaire.";
	}
	else{
		echo "Contactez l'administrateur pour corriger.";
	}
	echo "</p>\n";
}
//============================================

if(($_SESSION['statut']=="administrateur")||
(($_SESSION['statut']=="scolarite")&&(getSettingAOui('ScolEditElevesGroupes')))) {
?>
	<p align='center'><input type='submit' value='Enregistrer les modifications' /></p>
	<input type='hidden' name='id_classe' value='<?php echo $id_classe;?>' />
	<input type='hidden' name='login_eleve' value='<?php echo $login_eleve;?>' />
	<input type='hidden' name='is_posted' value='1' />
	<br />
	</form>
<?php
}
else{
	echo "<p><br /></p>\n";
}

require("../lib/footer.inc.php");
?>
