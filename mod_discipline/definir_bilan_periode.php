<?php
/*
 *
 * Copyright 2001-2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Check access
$sql = "SELECT 1=1 FROM droits WHERE id='/mod_discipline/definir_bilan_periode.php';";
$test = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($test) == 0) {
	$sql = "INSERT INTO droits VALUES ( '/mod_discipline/definir_bilan_periode.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les sanctions/avertissements de fin de période', '');";
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

require('sanctions_func_lib.php');

$msg="";

$suppr_type_avertissement = isset($_POST['suppr_type_avertissement']) ? $_POST['suppr_type_avertissement'] : NULL;
$avertissement_nom_court = isset($_POST['avertissement_nom_court']) ? $_POST['avertissement_nom_court'] : NULL;
$avertissement_nom_complet = isset($_POST['avertissement_nom_complet']) ? $_POST['avertissement_nom_complet'] : NULL;
$avertissement_description = isset($_POST['avertissement_description']) ? $_POST['avertissement_description'] : NULL;
$cpt = isset($_POST['cpt']) ? $_POST['cpt'] : 0;

if (isset($suppr_type_avertissement)) {
	check_token();

	$tab_type_avertissement_fin_periode=get_tab_type_avertissement();

	for ($i = 0; $i < count($suppr_type_avertissement); $i++) {
		if (isset($suppr_type_avertissement[$i])) {
			$sql="SELECT 1=1 FROM s_avertissements WHERE id_type_avertissement='".$suppr_type_avertissement[$i]."';";
			$test = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test)>0) {
				$msg.="Suppression du type '".$tab_type_avertissement_fin_periode['id_type_avertissement'][$suppr_type_avertissement[$i]]['nom_complet']."' impossible&nbsp;: ".mysqli_num_rows($test)." $mod_disc_terme_avertissement_fin_periode attribués à des élèves.<br />";
			}
			else {
				$sql = "DELETE FROM s_types_avertissements WHERE id_type_avertissement='$suppr_type_avertissement[$i]';";
				$suppr = mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$suppr) {
					$msg.="ERREUR lors de la suppression de l'".$mod_disc_terme_avertissement_fin_periode." n°" . $suppr_type_avertissement[$i] . ".<br />\n";
				} else {
					$msg.="Suppression de  l'".$mod_disc_terme_avertissement_fin_periode." n°" . $suppr_type_avertissement[$i] . ".<br />\n";
				}
			}
		}
	}
}

if ((isset($avertissement_nom_court))&&($avertissement_nom_court!='')&&(isset($avertissement_nom_complet))&&($avertissement_nom_complet!='')) {
	check_token();

	$tab_type_avertissement_fin_periode=get_tab_type_avertissement();

	for($loop=0;$loop<count($tab_type_avertissement_fin_periode['cpt']);$loop++) {
		if($tab_type_avertissement_fin_periode['cpt'][$loop]['nom_court']==$avertissement_nom_court) {
			$msg.="Le nom court de  l'".$mod_disc_terme_avertissement_fin_periode." proposé '$avertissement_nom_court' existe déjà.<br />\n";
		}
		elseif($tab_type_avertissement_fin_periode['cpt'][$loop]['nom_complet']==$avertissement_nom_complet) {
			$msg.="Le nom complet de  l'".$mod_disc_terme_avertissement_fin_periode." proposé '$avertissement_nom_complet' existe déjà.<br />\n";
		}
		else {
			$sql="INSERT INTO s_types_avertissements SET nom_court='$avertissement_nom_court',
											nom_complet='$avertissement_nom_complet'
											;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$res) {
				$msg.="ERREUR lors de l'ajout d'".$mod_disc_terme_avertissement_fin_periode.".<br />\n";
			}
			else {
				$msg.="Ajout d'".$mod_disc_terme_avertissement_fin_periode." effectué.<br />\n";
			}
		}
	}
}

// Begin standart header
$titre_page = "Définition des ".$mod_disc_terme_avertissement_fin_periode."s";
//====================================
// End standart header
require_once("../lib/header.inc.php");
if (!loadSettings()) {
	die("Erreur chargement settings");
}
//====================================

//debug_var();

$sql="SHOW TABLES LIKE 's_avertissements';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="CREATE TABLE IF NOT EXISTS s_avertissements (
	id_avertissement INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	login_ele VARCHAR( 50 ) NOT NULL ,
	id_type_avertissement INT(11),
	periode INT(11),
	date_avertissement DATE NOT NULL ,
	declarant VARCHAR( 50 ) NOT NULL ,
	commentaire TEXT NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$creation=mysqli_query($GLOBALS["mysqli"], $sql);
}

$sql="SHOW TABLES LIKE 's_types_avertissements';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="CREATE TABLE IF NOT EXISTS s_types_avertissements (
	id_type_avertissement INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	nom_court VARCHAR( 50 ) NOT NULL ,
	nom_complet VARCHAR( 255 ) NOT NULL,
	description TEXT NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$creation=mysqli_query($GLOBALS["mysqli"], $sql);

	echo "<p style='color:red'>".insere_avertissement_fin_periode_par_defaut()."</p>\n";
}

echo "<p class='bold'><a href='index.php'>Retour</a>";
echo "</p>\n";

echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset id='infosPerso' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		".add_token_field();

$tab_type_avertissement_fin_periode=get_tab_type_avertissement();
if(!isset($tab_type_avertissement_fin_periode['id_type_avertissement'])) {
	echo "
		<p style='color:red'>Aucun type d'$mod_disc_terme_avertissement_fin_periode n'est encore défini.</p>";
}
else {
	echo "
		<p>Liste des ".$mod_disc_terme_avertissement_fin_periode."s définis&nbsp;:</p>
		<table class='boireaus boireaus_alt' summary='Tableau des ".$mod_disc_terme_avertissement_fin_periode."s définis'>
			<tr>
				<th>Identifiant</th>
				<th>Nom court</th>
				<th>".ucfirst($mod_disc_terme_avertissement_fin_periode)."</th>
				<th>Supprimer</th>
			</tr>";

	foreach($tab_type_avertissement_fin_periode['id_type_avertissement'] as $key => $value) {
		echo "
			<tr>
				<td><label for='suppr_$key'>".$key."</label></td>
				<td><label for='suppr_$key'>".$value['nom_court']."</label></td>
				<td><label for='suppr_$key'>".$value['nom_complet']."</label></td>
				<td>";
		if($value['effectif']==0) {
			echo "<input type='checkbox' name='suppr_type_avertissement[]' id='suppr_$key' value='$key' />";
		}
		else {
			echo "<img src='../images/disabled.png' width='20' height='20' title=\"Suppression impossible: ".ucfirst($mod_disc_terme_avertissement_fin_periode)." donné à ".$value['effectif']." élève(s).\" alt='Suppression impossible' />";
		}
		echo "</td>
			</tr>";
	}
	echo "
		</table>";
}

echo "
		<p>".ucfirst($mod_disc_terme_avertissement_fin_periode)." à ajouter&nbsp;:<br />
		Nom complet&nbsp;: <input type='text' name='avertissement_nom_complet' value='' /><br />
		Nom court (sigle)&nbsp;: <input type='text' name='avertissement_nom_court' value='' maxlength='8' size='8' /></p>
		<p><input type='submit' name='valider' value='Valider' /></p>
	</fieldset>
</form>
<p><br /></p>

<p><i>NOTES</i>&nbsp;:</p>
<ul>
	<li>
		<p>L'intitulé <b>$mod_disc_terme_avertissement_fin_periode</b> peut être modifié dans la page d'<a href='../mod_discipline/discipline_admin.php'>activation/paramétrage du module Discipline</a></p>
	</li>
	<li>
		<p>Les $mod_disc_terme_avertissement_fin_periode sont destinés à être donnés lors du conseil de classe.<br />
		Ils donnent lieu à l'édition d'un document indépendant du bulletin scolaire.<br />
		Les avertissements, sanctions,... ne sont pas destinés à subsister au-delà de l'année en cours.</p>
	</li>
	<li>
		<p><b>Extrait de l'article R511-13 du code de l'éducation : </b>Il rappelle que dans les lycées et collèges relevant du ministre chargé de l'éducation, les sanctions qui peuvent être prononcées à l'encontre des élèves sont les suivantes :</p>
		<ol>
			<li>L'avertissement</li>
			<li>Le blâme ;</li>
			<li>La mesure de responsabilisation ;</li>
			<li>L'exclusion temporaire de la classe. Pendant l'accomplissement de la sanction, l'élève est accueilli dans l'établissement. La durée de cette exclusion ne peut excéder huit jours ;</li>
			<li>L'exclusion temporaire de l'établissement ou de l'un de ses services annexes. La durée de cette exclusion ne peut excéder huit jours ;</li>
			<li>L'exclusion définitive de l'établissement ou de l'un de ses services annexes.</li>
		</ol>
		<p>Les sanctions peuvent être assorties d'un sursis total ou partiel.<br />
		<b>L'avertissement, le blâme et la mesure de responsabilisation sont effacés du dossier administratif de l'élève à l'issue de l'année scolaire.</b></br> 
		Les autres sanctions, hormis l'exclusion définitive, sont effacées du dossier administratif de l'élève au bout d'un an.</br></br>
		Le règlement intérieur reproduit l'échelle des sanctions et prévoit les mesures de prévention et d'accompagnement ainsi que les modalités de la mesure de responsabilisation.<br />
		<a href='http://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006071191&idArticle=LEGIARTI000020663068&dateTexte=&categorieLien=cid'>Le lien sur Légifrance</a>.</p>
	</li>
</ul>\n";

require("../lib/footer.inc.php");
die();
?>
