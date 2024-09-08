<?php
/*
 *
 * Copyright 2001, 2024 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//INSERT INTO `droits` VALUES ('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Affectation des comptes scolarité aux classes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$quitter_la_page=isset($_POST['quitter_la_page']) ? $_POST['quitter_la_page'] : (isset($_GET['quitter_la_page']) ? $_GET['quitter_la_page'] : NULL);
$nettoyage_assoc=isset($_GET['nettoyage_assoc']) ? $_GET['nettoyage_assoc'] : NULL;

if(isset($_GET['nettoyage_assoc']) and ($_GET['nettoyage_assoc'] == "y") and (isset($_GET['login_user']))) {
	check_token();

	$sql="DELETE FROM j_scol_classes WHERE login='".$_GET['login_user']."';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0){
		$msg = "Associations avec le compte ".$_GET['login_user']." supprimées.<br />";
	}
	else {
		$msg = "Erreur lors de la suppression des associations avec le compte ".$_GET['login_user'].".<br />";
	}
}

if (isset($_POST['action']) and ($_POST['action'] == "reg_scolresp") and (isset($_POST['scol_login'])) and (isset($_POST['tab_id_clas']))) {
	check_token();

	$msg = '';
	$notok = false;
	
	$scol_login=$_POST['scol_login'];
	$tab_id_clas=$_POST['tab_id_clas'];

	for($j=0;$j<count($tab_id_clas);$j++){
		for($i=0;$i<count($scol_login);$i++){
			if(isset($_POST['case_'.$i.'_'.$j])){
				$test=mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM j_scol_classes WHERE id_classe='".$tab_id_clas[$j]."' AND login='".$scol_login[$i]."'");
				if(mysqli_num_rows($test)==0){
					$sql="INSERT INTO j_scol_classes SET id_classe='".$tab_id_clas[$j]."', login='".$scol_login[$i]."'";
					$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$reg_data){
						$msg.= "Erreur lors de l'insertion d'un nouvel enregistrement $tab_id_clas[$j] pour $scol_login[$i].";
						$notok = true;
					}
				}
				// Sinon: l'enregistrement est déjà présent.
			}
			else{
				$test=mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM j_scol_classes WHERE id_classe='".$tab_id_clas[$j]."' AND login='".$scol_login[$i]."'");
				if(mysqli_num_rows($test)>0){
					$sql="DELETE FROM j_scol_classes WHERE id_classe='".$tab_id_clas[$j]."' AND login='".$scol_login[$i]."'";
					$reg_data=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$reg_data){
						$msg.= "Erreur lors de la suppression de l'enregistrement $tab_id_clas[$j] pour $scol_login[$i].";
						$notok = true;
					}
				}
			}
		}
	}



/*
	for($i=0;$i<$nombre_lignes;$i++){
		$id_classe = old_mysql_result($call_data, $i, "id");
		if (isset($_POST[$id_classe]) and ($_POST[$id_classe] == "yes")) {
			$test=mysql_query("SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe' AND login='".$_POST['reg_scollogin']."'");
			if(mysql_num_rows($test)==0){
				$sql="INSERT INTO j_scol_classes SET id_classe='$id_classe', login='".$_POST['reg_scollogin']."'";
				$reg_data=mysql_query($sql);
				if(!$reg_data){
					$msg.= "Erreur lors lors de l'insertion d'un nouvel enregistrement.";
					$notok = true;
				}
			}
		}
		else{
			$test=mysql_query("SELECT 1=1 FROM j_scol_classes WHERE id_classe='$id_classe' AND login='".$_POST['reg_scollogin']."'");
			if(mysql_num_rows($test)>0){
				$sql="DELETE FROM j_scol_classes WHERE id_classe='$id_classe' AND login='".$_POST['reg_scollogin']."'";
				$res_suppr=mysql_query($sql);
				if(!$res_suppr){
					$msg.="Erreur lors lors de la suppression de l'enregistrement $id_classe.";
					$notok = true;
				}
			}
		}
	}
	*/

	if ($notok == true) {
		$msg .= "Il y a eu des erreurs lors de l'enregistrement des données";
	} else {
		$msg .= "L'enregistrement des données s'est bien passé.";
	}
}



$disp_filter = null;
if (isset($_GET['disp_filter'])) {
	$disp_filter = $_GET['disp_filter'];
} else {
	$disp_filter = "only_undefined";
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Gestion des classes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

echo "<form name='setScolResp' action='scol_resp.php?disp_filter=" . $disp_filter . "' method='post'>\n";
echo add_token_field();

if(!isset($quitter_la_page)){
	echo "<p class='bold'>";
	echo "<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>";

	if($_SESSION['statut']=='administrateur') {
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
		echo " | <a href='cpe_resp.php'>Paramétrage CPE</a> | <a href='prof_suivi.php'>Paramétrage ".$gepi_prof_suivi."</a>";
	}

	echo "</p>\n";
}
else{
	// Cette page a été ouverte en target='blank' depuis une autre page (par exemple /eleves/modify_eleve.php)
	// Après modification éventuelle, il faut quitter cette page.
	echo "<p class='bold'>";
	echo "<a href='index.php' onClick=\"if(confirm_abandon (this, change, '$themessage')){self.close()};return false;\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Refermer la page </a>";

	if($_SESSION['statut']=='administrateur') {
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
		echo " | <a href='cpe_resp.php'>Paramétrage CPE</a> | <a href='prof_suivi.php'>Paramétrage ".$gepi_prof_suivi."</a>";
	}

	echo "</p>\n";

	echo "<input type='hidden' name='quitter_la_page' value='y' />\n";
	// Il va falloir faire en sorte que la page destination tienne compte de la variable...
}
?>

<p>Affectez les classes aux comptes scolarité.</p>
<!--p><a href="scol_resp.php?disp_filter=all">Afficher toutes les classes</a> || <a href="scol_resp.php?disp_filter=only_undefined">Afficher les classes non-paramétrées</a></p-->
<?php
	$acces_utilisateur_modify=acces("/utilisateurs/modify_user.php", $_SESSION['statut']);

	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes ORDER BY classe;");
	$nombre_lignes = mysqli_num_rows($call_data);
	if($nombre_lignes==0) {
		echo "<p style='color:red'>Il n'existe encore aucune classe.<br />
		Commencez par créer des classes.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$call_scol = mysqli_query($GLOBALS["mysqli"], "SELECT login,nom,prenom FROM utilisateurs WHERE (statut='scolarite' AND etat='actif') ORDER BY nom,prenom");
	$nb = mysqli_num_rows($call_scol);
	if($nb==0) {
		echo "<p style='color:red'>Il n'existe encore aucun compte scolarité.<br />
		Commencez par créer des comptes de statut 'scolarite'.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo "<table class='boireaus'>\n";
	$ligne_comptes_scol="<tr style='background-color:#FAFABE;'>\n";
	$ligne_comptes_scol.="<th style='text-align:center; font-weight:bold;'>Comptes</th>\n";

	$i=0;
	$scol_login=array();
	while($lig_scol=mysqli_fetch_object($call_scol)){
		//$ligne_comptes_scol.="<td style='text-align:center; font-weight:bold;'>$lig_scol->prenom $lig_scol->nom</td>\n";
		if($acces_utilisateur_modify) {
			$ligne_comptes_scol.="<th style='text-align:center; font-weight:bold;'><a href='../utilisateurs/modify_user.php?user_login=".$lig_scol->login."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Accéder à la fiche utilisateur.\">$lig_scol->prenom $lig_scol->nom</a></th>\n";
		}
		else {
			$ligne_comptes_scol.="<th style='text-align:center; font-weight:bold;'>$lig_scol->prenom $lig_scol->nom</th>\n";
		}
		$scol_login[$i]=$lig_scol->login;
		$i++;
	}
	//$ligne_comptes_scol.="<td>\n";
	//$ligne_comptes_scol.="&nbsp;\n";
	//$ligne_comptes_scol.="</td>\n";
	$ligne_comptes_scol.="<th>\n";
	$ligne_comptes_scol.="&nbsp;\n";
	$ligne_comptes_scol.="</th>\n";
	$ligne_comptes_scol.="</tr>\n";
	echo $ligne_comptes_scol;

	echo "<tr style='background-color:#FAFABE;'>\n";
	//echo "<td style='text-align:center; font-weight:bold;'>Classes</td>\n";
	echo "<th style='text-align:center; font-weight:bold;'>Classes</th>\n";
	for($i=0;$i<$nb;$i++){
		//echo "<td style='text-align:center;'>\n";
		echo "<th style='text-align:center;'>\n";

		//echo "<a href='javascript:modif_case($i,true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		echo "<a href=\"javascript:modif_case($i,true,'col');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		//echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
		echo "<a href=\"javascript:modif_case($i,false,'col');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

		echo "<input type='hidden' name='scol_login[$i]' value='$scol_login[$i]' />";
		//echo "</td>\n";
		echo "</th>\n";
	}
	//echo "<td>\n";
	echo "<th>\n";
	echo "&nbsp;\n";
	//echo "</td>\n";
	echo "</th>\n";
	echo "</tr>\n";

	if ($nombre_lignes != 0) {
		// Lignes classes...
		$j=0;
		$alt=1;
		while($lig_clas=mysqli_fetch_object($call_data)){
			if(($j%10==0)&&$j>0){echo $ligne_comptes_scol;}

			$alt=$alt*(-1);

			//if($j%2==0){$bgcolor="style='background-color: gray;'";}else{$bgcolor='';}
			//echo "<tr $bgcolor>\n";
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td style='text-align:center;'>";
			echo "<input type='hidden' name='tab_id_clas[$j]' value='$lig_clas->id' />\n";
			echo "$lig_clas->classe";
			echo "</td>\n";
			for($i=0;$i<$nb;$i++){
				$test=mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM j_scol_classes WHERE id_classe='".$lig_clas->id."' AND login='".$scol_login[$i]."'");
				//if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: #AAE6AA;";}
				if(mysqli_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: plum;";}

				echo "<td style='text-align:center;$bgcolor'>\n";
				echo "<input type='checkbox' name='case_".$i."_".$j."' id='case_".$i."_".$j."' value='y' onchange='changement();' $checked/>\n";
				echo "</td>\n";
			}
			echo "<td>\n";
			echo "<a href=\"javascript:modif_case($j,true,'lig');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			//echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "<a href=\"javascript:modif_case($j,false,'lig');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "</td>\n";
			echo "</tr>\n";
			$j++;
		}

		echo "</table>\n";
		echo "<input type='hidden' name='action' value='reg_scolresp' />\n";
		echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";

		echo "<p style='margin-top:1em; margin-left:4.5em; text-indent:-4.5em'><em>NOTES&nbsp;:</em> Seuls les comptes actifs sont présentés ci-dessus.<br />";
		$sql="SELECT DISTINCT u.login, civilite, nom, prenom, statut FROM utilisateurs u, j_scol_classes jsc WHERE u.login=jsc.login ORDER BY u.nom, u.prenom;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			while($lig=mysqli_fetch_object($res)) {
				if(!in_array($lig->login, $scol_login)) {
					echo "<br />Le compte $lig->login ($lig->statut) de $lig->civilite $lig->nom $lig->prenom est associé à une ou des classes.<br />Si ce compte a été désactivé accidentellement, vous devriez le <a href='../utilisateurs/modify_user.php?user_login=$lig->login' onclick=\"return confirm_abandon (this, change, '$themessage')\">ré-activer</a>.<br />Si en revanche, ce compte ne doit plus être associé à des classes, vous devriez <a href='".$_SERVER['PHP_SELF']."?nettoyage_assoc=y&amp;login_user=".$lig->login.add_token_in_url()."' onclick=\"return confirm_abandon (this, change, '$themessage')\">supprimer les associations compte/classe pour $lig->login</a> pour éviter qu'il reçoive des mails concernant ces classes.<br />";
				}
			}
		}
		echo "</p>";

	} else {
		echo "</table>\n";
		echo "<p class='grand'><b>Attention :</b> aucune classe n'a été définie dans la base GEPI !</p>\n";
	}






	//============================================
	// AJOUT: boireaus
	echo "<script type='text/javascript' language='javascript'>
		/*
		function modif_case(id_login,statut){
			// id_login: numéro de colonne correspondant au login
			// statut: true ou false
			for(k=0;k<$nombre_lignes;k++){
				if(document.getElementById('case_'+id_login+'_'+k)){
					document.getElementById('case_'+id_login+'_'+k).checked=statut;
				}
			}
			changement();
		}
		*/

		function modif_case(id,statut,mode){
			// id: numéro de:
			//					. colonne correspondant au login
			//					. ligne
			// statut: true ou false
			// mode: col ou lig
			if(mode=='col'){
				for(k=0;k<$nombre_lignes;k++){
					if(document.getElementById('case_'+id+'_'+k)){
						document.getElementById('case_'+id+'_'+k).checked=statut;
					}
				}
			}
			else{
				for(k=0;k<".count($scol_login).";k++){
					if(document.getElementById('case_'+k+'_'+id)){
						document.getElementById('case_'+k+'_'+id).checked=statut;
					}
				}
			}
			changement();
		}
	</script>\n";
	//============================================


/*
	echo "<p><select size = 1 name='reg_scollogin'>\n";
	$scolresp = "vide";
	$call_scol = mysql_query("SELECT login,nom,prenom FROM utilisateurs WHERE (statut='scolarite' AND etat='actif') ORDER BY nom,prenom");
	$nb = mysql_num_rows($call_scol);
	for ($i="0";$i<$nb;$i++) {
		$scolresp = old_mysql_result($call_scol, $i, "login");
		$scolresp_nom = old_mysql_result($call_scol, $i, "nom");
		$scolresp_prenom = old_mysql_result($call_scol, $i, "prenom");
		echo "<option value='$scolresp'>" . $scolresp_prenom . " " . $scolresp_nom ;
		echo "</option>\n";
	}
	echo "</select>";
	// On va chercher les classes déjà existantes, et on les affiche.

	$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
	$nombre_lignes = mysql_num_rows($call_data);

	if ($nombre_lignes != 0) {
		$flag = 1;
		echo "<table style='margin-left: 50px;' cellpadding=3 cellspacing=0 border=0>\n";
		$i = 0;
		while ($i < $nombre_lignes){
			$id_classe = old_mysql_result($call_data, $i, "id");
			$classe = old_mysql_result($call_data, $i, "classe");

			$test_existing = old_mysql_result(mysql_query("select count(*) total FROM j_scol_classes WHERE id_classe='$id_classe'"), "0", "total");

			if ($disp_filter == "all" OR ($disp_filter == "only_undefined" AND $test_existing == "0")) {
				echo "<tr";
				if ($flag=="1") {
					echo " class='fond_sombre'";
					$flag = "0";
				} else {
					$flag=1;
				}

				echo ">\n";
				echo "<td><input type='checkbox' id='classe_".$i."' name='".$id_classe."' value='yes' /></td>\n";
				echo "<td><b>$classe</b></td>\n";
				echo "</tr>\n";
			}
			$i++;
		}
		echo "</table>\n";



		//============================================
		// AJOUT: boireaus
		echo "<script type='text/javascript' language='javascript'>
			function modif_cases(statut){
				// statut: true ou false
				for(k=0;k<$nombre_lignes;k++){
					if(document.getElementById('classe_'+k)){
						document.getElementById('classe_'+k).checked=statut;
					}
				}
				changement();
			}
		</script>\n";
		//============================================

		echo "<a href='javascript:modif_cases(true)'>Tout cocher</a>/\n";
		echo "<a href='javascript:modif_cases(false)'>Tout décocher</a>\n";


		echo "<input type='hidden' name='action' value='reg_scolresp' />\n";
		echo "<p><input type='submit' value='Enregistrer' /></p>\n";

	} else {
		echo "<p class='grand'>Attention : aucune classe n'a été définie dans la base GEPI !</p>";
	}
*/
?>
</form>
<?php require("../lib/footer.inc.php");?>
