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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

$sql="SELECT 1=1 FROM droits WHERE id='/saisie/socle_verif.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/saisie/socle_verif.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Socle: Vérification du remplissage',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui("SocleSaisieComposantes")) {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

/*
if(!getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"])) {
	if(($_SESSION['statut']=="professeur")&&(getSettingAOui("SocleSaisieComposantes_PP"))&&(is_pp($_SESSION["login"]))) {
		// Accès autorisé
	}
	else {
		header("Location: ../accueil.php?msg=Accès non autorisé");
		die();
	}
}
*/
//==============================================================
// Pour tenir compte d'un ajout de champ 'annee' oublié en 1.7.1
check_tables_modifiees();
//==============================================================

$gepiYear=getSettingValue("gepiYear");
$gepiYear_debut=mb_substr($gepiYear, 0, 4);
if(!preg_match("/^20[0-9]{2}/", $gepiYear_debut)) {
	header("Location: ../accueil.php?msg=Année scolaire non définie dans Gestion générale/Configuration générale.");
	die();
}

//==============================================================
// Pour tenir compte d'un ajout de champ 'annee' oublié en 1.7.1
check_tables_modifiees();
//==============================================================

$msg="";
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);

$tab_mes_classes=array();
$tab_mes_classes_txt=array();
$tab_mes_classes_lien=array();
$sql=retourne_sql_mes_classes();
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while($lig=mysqli_fetch_object($res)) {
		$tab_mes_classes[]=$lig->id_classe;
		$tab_mes_classes_txt[]=preg_replace("/ /", "&nbsp;", $lig->classe);
		$tab_mes_classes_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig->id_classe;
	}
}

if((isset($id_classe))&&(!in_array($id_classe, $tab_mes_classes))) {
	$msg="Vous n'avez pas accès à cette classe.<br />";
	unset($id_classe);
}


// Etat d'ouverture ou non des saisies
$max_per=0;
$sql="SELECT MAX(num_periode) AS max_per FROM periodes;";
$res_max=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_max)==0) {
	echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> Aucune classe avec périodes ne semble définie.</p>";
	require("../lib/footer.inc.php");
	die();
}
$lig_max=mysqli_fetch_object($res_max);
$max_per=$lig_max->max_per;

$SocleOuvertureSaisieComposantes=array();
for($i=1;$i<$max_per+1;$i++) {
	$SocleOuvertureSaisieComposantes[$i]=getSettingAOui("SocleOuvertureSaisieComposantesPeriode".$i);
}

$tab_domaine_socle=array();
$tab_domaine_socle["CPD_FRA"]="Comprendre, s'exprimer en utilisant la langue française à l'oral et à l'écrit";
$tab_domaine_socle["CPD_ETR"]="Comprendre, s'exprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale";
$tab_domaine_socle["CPD_SCI"]="Comprendre, s'exprimer en utilisant les langages mathématiques, scientifiques et informatiques";
$tab_domaine_socle["CPD_ART"]="Comprendre, s'exprimer en utilisant les langages des arts et du corps";
$tab_domaine_socle["MET_APP"]="Les méthodes et outils pour apprendre";
$tab_domaine_socle["FRM_CIT"]="La formation de la personne et du citoyen";
$tab_domaine_socle["SYS_NAT"]="Les systèmes naturels et les systèmes techniques";
$tab_domaine_socle["REP_MND"]="Les représentations du monde et l'activité humaine";

//debug_var();

$themessage  = 'Des valeurs ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Vérif. Socle";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

//$SocleOuvertureSaisieComposantes=getSettingAOui("SocleOuvertureSaisieComposantes");

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' id='form_choix_classe'>
<p class='bold'><a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if((acces("/saisie/socle_verrouillage.php", $_SESSION["statut"]))&&(
	(getSettingAOui("SocleOuvertureSaisieComposantes_".$_SESSION["statut"]))||
	((getSettingAOui("SocleOuvertureSaisieComposantes_PP"))&&(is_pp($_SESSION["login"])))
)) {
	echo " | <a href=\"socle_verrouillage.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Ouverture/verrouillage des saisies</a>";
}

if((acces("/saisie/socle_import.php", $_SESSION["statut"]))&&
	(getSettingAOui("SocleImportComposantes"))&&
	(($_SESSION['statut']=="administrateur")||(getSettingAOui("SocleImportComposantes_".$_SESSION["statut"])))) {
	echo " | <a href=\"socle_import.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Import des bilans de composantes du socle</a>";
}

if(!isset($id_classe)) {
	if((acces("/saisie/saisie_socle.php", $_SESSION["statut"]))&&(getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"]))) {
		echo " | <a href=\"saisie_socle.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie des bilans de composantes du socle</a>";
	}
	echo "</p>
</form>";

	echo "<h2>Import des saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

	if(count($tab_mes_classes)>0) {
		$nbcol=3;

		echo "<p>Pour quelle classe souhaitez-vous contrôler le remplissage des Bilans de Composantes du Socle&nbsp;?</p>";
		echo tab_liste($tab_mes_classes_txt, $tab_mes_classes_lien, $nbcol);
	}
	else {
		echo "<p style='color:red'>Aucune classe n'a été trouvée.</p>";
	}

		echo "<p style='margin-top:1em'><em>Notes&nbsp;:</em></p>
<ul>
	<li><p>Si vous souhaitez une remontée périodique de l'état de maitrise des Composantes du socle, il convient de saisir un positionnement pour chacun des domaines.<br />
	Si un domaine n'est pas renseigné pour un élève, aucun bilan du socle ne sera remonté pour l'élève en question.</p></li>
	<li><p>Pour les bilans de fin de cycle en 6ème et 3ème, il faut en plus que la saisie d'une synthèse soit effectuée.</p></li>
</ul>";

	require("../lib/footer.inc.php");
	die();
}
elseif(!isset($periode)) {
	if((acces("/saisie/saisie_socle.php", $_SESSION["statut"]))&&(getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"]))) {
		echo " | <a href=\"saisie_socle.php?id_classe=$id_classe\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie des bilans de composantes du socle</a>";
	}
	echo "</p>
</form>";

	echo "<h2>Import des saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

	echo "<h3>Vérification du remplissage des Composantes du socle pour la classe de ".get_nom_classe($id_classe)."</h3>";
	$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe';";
	$res_max=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_max)==0) {
		echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> La classe n'a pas de périodes définies.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_max=mysqli_fetch_object($res_max);

	echo "<p style='margin-left:3em;text-indent:-3em;'>Choisissez la période&nbsp;:<br />";
	for($i=1;$i<$max_per+1;$i++) {
		$etat_periode="";
		if(!$SocleOuvertureSaisieComposantes[$i]) {
			$etat_periode=" (période close)";
		}
		echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_classe."&periode=".$i."'>Période $i</a>".$etat_periode."<br />";
	}
	echo "</p>";

		echo "<p style='margin-top:1em'><em>Notes&nbsp;:</em></p>
<ul>
	<li><p>Si vous souhaitez une remontée périodique de l'état de maitrise des Composantes du socle, il convient de saisir un positionnement pour chacun des domaines.<br />
	Si un domaine n'est pas renseigné pour un élève, aucun bilan du socle ne sera remonté pour l'élève en question.</p></li>
	<li><p>Pour les bilans de fin de cycle en 6ème et 3ème, il faut en plus que la saisie d'une synthèse soit effectuée.</p></li>
</ul>";

	require("../lib/footer.inc.php");
	die();
}

if((acces("/saisie/saisie_socle.php", $_SESSION["statut"]))&&(getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"]))) {
	if((getSettingAOui("SocleSaisieComposantes_PP"))&&(is_pp($_SESSION["login"],$id_classe))) {
		echo " | <a href=\"saisie_socle.php?id_classe=$id_classe&periode=$periode\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie des bilans de composantes du socle</a>";
	}
	else {
		echo " | <a href=\"saisie_socle.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie des bilans de composantes du socle</a>";
	}
}

$classe=get_nom_classe($id_classe);
echo " | <a href=\"socle_verif.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre classe</a>
 | <a href=\"socle_verif.php?id_classe=$id_classe\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre période de $classe</a>
 | Même période&nbsp;:
<input type='hidden' name='periode' value='$periode' />
 <select name='id_classe' onchange=\"document.getElementById('form_choix_classe').submit();\">";
for($loop=0;$loop<count($tab_mes_classes);$loop++) {
	$selected="";
	if($tab_mes_classes[$loop]==$id_classe) {
		$selected=" selected='true'";
	}
	echo "
	<option value='".$tab_mes_classes[$loop]."'".$selected.">".$tab_mes_classes_txt[$loop]."</option>";
}
echo "
</select>
</p>
</form>

<h2>Import des saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>

<h3>".$classe." (période $periode)</h3>";

$tab_ele_saisie_incomplete=array();
$sql="SELECT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$id_classe."' AND jec.periode='$periode' ORDER BY e.nom, e.prenom;";
//echo "$sql<br />";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while($lig=mysqli_fetch_assoc($res)) {
		$mef_code_ele=$lig['mef_code'];
		if(!isset($tab_cycle[$mef_code_ele])) {
			$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
			$cycle=$tmp_tab_cycle_niveau["mef_cycle"];
			$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
			$tab_cycle[$mef_code_ele]=$cycle;
		}

		if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
			echo "
	<p style='color:red'>Le cycle courant pour ".$lig['nom']." ".$lig['prenom']." n'a pas pu être identitfié&nbsp;???</p>";
		}
		else {
			foreach($tab_domaine_socle as $code => $libelle) {
				$sql="SELECT 1=1 FROM socle_eleves_composantes WHERE ine='".$lig['no_gep']."' AND cycle='".$tab_cycle[$mef_code_ele]."' AND code_composante='".$code."' AND periode='$periode' AND annee='".$gepiYear_debut."';";
				//echo "$sql<br />";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)==0) {
					if(!array_key_exists($lig['login'], $tab_ele_saisie_incomplete)) {
						$tab_ele_saisie_incomplete[$lig['login']]=$lig;
					}
					$tab_ele_saisie_incomplete[$lig['login']]["code_composante"][$code]="vide";
				}
			}

			$sql="SELECT 1=1 FROM socle_eleves_syntheses WHERE ine='".$lig['no_gep']."' AND cycle='".$tab_cycle[$mef_code_ele]."' AND synthese!='' AND annee='".$gepiYear_debut."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				if(!array_key_exists($lig['login'], $tab_ele_saisie_incomplete)) {
					$tab_ele_saisie_incomplete[$lig['login']]=$lig;
				}
				$tab_ele_saisie_incomplete[$lig['login']]["synthese_vide"]="y";
			}
		}
	}
	/*
	echo "<pre>";
	print_r($tab_ele_saisie_incomplete);
	echo "</pre>";
	*/
	if(count($tab_ele_saisie_incomplete)>0) {
		echo "<p>Les saisies sont <strong>incomplètes</strong> pour le ou les élèves suivants&nbsp;:</p>
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th rowspan='2'>Élève</th>
			<th rowspan='2'>Classe</th>
			<th colspan='".count($tab_domaine_socle)."'>Composantes</th>
			<th rowspan='2'>Synthèse</th>
		</tr>
		<tr>";
		foreach($tab_domaine_socle as $code => $libelle) {
			echo "
			<th title=\"$libelle\">".$code."</th>";
		}
		echo "
		</tr>
	</thead>
	<tbody>";
		foreach($tab_ele_saisie_incomplete as $login_ele => $current_tab) {
			echo "
		<tr onmouseover=\"this.style.backgroundColor='white';\" onmouseout=\"this.style.backgroundColor='';\">
			<td>".$current_tab["nom"]." ".$current_tab["prenom"]."</td>
			<td>".$classe."</td>";
		foreach($tab_domaine_socle as $code => $libelle) {
			echo "
			<td title=\"$libelle\">".(isset($current_tab["code_composante"][$code]) ? "<img src='../images/disabled.png' class='icone16' alt='Vide' />" : "<img src='../images/enabled.png' class='icone16' alt='Ok' />")."</td>";
		}
		echo "
			<td>".(isset($current_tab["synthese_vide"]) ? "<img src='../images/disabled.png' class='icone16' alt='Vide' />" : "<img src='../images/enabled.png' class='icone16' alt='Ok' />")."</td>
		</tr>";
		}
		echo "
	</tbody>
</table>";
	}
	else {
		echo "<p>Les bilans de composantes du socle et les synthèses sont remplis pour tous les élèves de la classe.</p>";
	}

	echo "<p style='margin-top:1em'><em>Notes&nbsp;:</em></p>
<ul>
	<li><p>Si vous souhaitez une remontée périodique de l'état de maitrise des Composantes du socle, il convient de saisir un positionnement pour chacun des domaines.<br />
	Si un domaine n'est pas renseigné pour un élève, aucun bilan du socle ne sera remonté pour l'élève en question.</p></li>
	<li><p>Pour les bilans de fin de cycle en 6ème et 3ème, il faut en plus que la saisie d'une synthèse soit effectuée.</p></li>
</ul>";

	echo "
<p><br /></p>";
}

require("../lib/footer.inc.php");
?>
