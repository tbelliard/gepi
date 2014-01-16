<?php
/*
* $Id$
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
//require_once("../lib/initialisationsPropel.inc.php");
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

// ajout des droits pour scolarité en 1.6.3
$sql = "UPDATE `gepi`.`droits` SET `scolarite` = 'V' WHERE `droits`.`id` = '/mod_abs2/export_stat.php';";
$resp=mysqli_query($GLOBALS["mysqli"], $sql);
//INSERT INTO droits SET id='/mod_abs2/export_stat.php',administrateur='V',professeur='F',cpe='V',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Exports statistiques',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

//recherche de l'utilisateur avec propel
/*
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}
*/

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

include_once 'lib/function.php';

$msg="";

$mois=isset($_POST['mois']) ? $_POST['mois'] : (isset($_GET['mois']) ? $_GET['mois'] : NULL);
$annee=isset($_POST['annee']) ? $_POST['annee'] : (isset($_GET['annee']) ? $_GET['annee'] : NULL);

$tab_stat=array();

// Problème pour les cités scolaires...
$tab_stat['RNE']=getSettingValue('gepiSchoolRne');
$tab_stat['UAI']=getSettingValue('gepiSchoolRne');
// Proposer le choix des MEF à retenir? et de modifier le RNE avant export?

$extraire="n";
if(isset($mois)) {
	if((!preg_match("/^[0-9]*$/", $mois))||($mois<1)||($mois>12)) {
		$msg.="Le mois choisi '$mois' n'est pas valide.<br />";
		unset($mois);
	}
	elseif(!isset($annee)) {
		$msg.="L'année n'a pas été choisie.<br />";
	}
	elseif(!preg_match("/^[0-9]{4}-[0-9]{4}$/", $annee)) {
		$msg.="L'année choisie '$annee' n'a pas un format valide.<br />";
		unset($annee);
	}
	else {
		$tab_annee=explode("-",$annee);
		if($mois<9) {
			$annee_extract=$tab_annee[1];
		}
		else {
			$annee_extract=$tab_annee[0];
		}

		$extraire="y";
	}
}

if($extraire=="y") {
	$tab_stat['mois']=strftime("%b-%y", strtotime($mois."/01/$annee_extract"));
	$tab_stat['ville']=getSettingValue('gepiSchoolCity');

	$mois_suiv=$mois+1;
	$annee_mois_suiv=$annee_extract;
	if($mois==12) {
		$mois_suiv=+1;
		$annee_mois_suiv=$annee_extract+1;
	}

	// Il faudrait un champ eleves.date_entree pour repérer les élèves arrivés en cours d'année.
	//$tab_stat['effectif_total']=-1;
	$sql="SELECT DISTINCT e.nom,e.login,e.date_sortie FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND (date_sortie IS NULL OR date_sortie>'".$annee_extract."-".$mois."-01 00:00:00');";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$tab_stat['effectif_total']=mysqli_num_rows($res);

	// Recherche des mef associés à des élèves:
	$cpt_mef=0;
	$sql="SELECT * FROM mef WHERE mef_code IN (SELECT DISTINCT mef_code FROM eleves) ORDER BY libelle_court, libelle_long;";
	$res_mef=mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt_mef=0;
	while($lig_mef=mysqli_fetch_object($res_mef)) {
		$tab_stat['mef'][$cpt_mef]['mef_code']=$lig_mef->mef_code;
		$tab_stat['mef'][$cpt_mef]['libelle_court']=$lig_mef->libelle_court;
		$tab_stat['mef'][$cpt_mef]['libelle_long']=$lig_mef->libelle_long;
		$tab_stat['mef'][$cpt_mef]['libelle_edition']=$lig_mef->libelle_edition;

		//======================================
		// A partir de 4 demi-journées
		// Non justifiée:
		$sql="SELECT e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e  
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND
		 a.non_justifiee!='0' AND 
		 a.eleve_id=e.id_eleve AND 
		 e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		 GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=4;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['nj_sup_egal_4']=mysqli_num_rows($res);

		// Aucun motif (i.e. non valable)
		// Comment récupérer ça?
		//======================================

		//======================================
		// De 4 à 10 demi-journées
		$sql="SELECT e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e  
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND
		 a.non_justifiee!='0' AND 
		 a.eleve_id=e.id_eleve AND 
		 e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		 GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=4 and COUNT(a.non_justifiee)<=10;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['nj_4_a_10']=mysqli_num_rows($res);

		// Aucun motif (i.e. non valable)
		// Comment récupérer ça?
		//======================================

		//======================================
		// A partir de 11 demi-journées
		$sql="SELECT e.nom,e.prenom,a.eleve_id,count(a.non_justifiee) FROM a_agregation_decompte a, eleves e  
		WHERE a.date_demi_jounee>='".$annee_extract."-".$mois."-01 00:00:00' AND 
		a.date_demi_jounee<'".$annee_mois_suiv."-".$mois_suiv."-01 00:00:00' AND
		 a.non_justifiee!='0' AND 
		 a.eleve_id=e.id_eleve AND 
		 e.mef_code IN (SELECT DISTINCT mef_code FROM mef WHERE mef_rattachement='".$lig_mef->mef_code."') 
		 GROUP BY a.eleve_id HAVING COUNT(a.non_justifiee)>=11;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$tab_stat['mef'][$cpt_mef]['nj_sup_egal_11']=mysqli_num_rows($res);

		// Aucun motif (i.e. non valable)
		// Comment récupérer ça?
		//======================================

		$cpt_mef++;
	}

}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";

$javascript_specifique[] = "lib/tablekit";
//$dojo=true;
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Exports statistiques";
require_once("../lib/header.inc.php");
//**************** EN-TETE *****************
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');

?>
<div id="contain_div" class="css-panes">
     <?php if (isset($message)){
      echo'<h2 class="no">'.$message.'</h2>';
    }?>
<?php

//echo "<p style='color:red; font-weight:bold'>Cette page, réclamée peu de temps avant la sortie de la 1.6.3, est inachevée.</p>\n";

// Choix du mois
if(!isset($mois)) {
	$mois=strftime("%m")-1;
	if($mois==0) {$mois=12;}
	echo "<form id='choix_mois' name='choix_mois' action='".$_SERVER['PHP_SELF']."' method='post'>
	<p>Pour quel mois souhaitez-vous extraire les statistiques&nbsp;?<br />\n";
	for($loop=1;$loop<=12;$loop++) {
		echo "<input type='radio' name='mois' id='mois_$loop' value='$loop' ";
		if($loop==$mois) {echo "checked ";}
		echo "/><label for='mois_$loop'> ".strftime("%B", strtotime($loop."/01/2000"))."</label><br />\n";
	}

	if(date("n")<9) {
		$annee_courante=(date("Y")-1)."-".date("Y");
	}
	else {
		$annee_courante=date("Y")."-".(date("Y")+1);
	}

	echo "	<p>Année scolaire&nbsp;: <input type='text' name='annee' id='annee' value='".$annee_courante."' size='7' /></p>
	<p><input type='submit' value='Valider' /></p>
</form>

<p><br /></p>
<ul style='color:red; list-style-type:circle;'>
	<li>- Proposer plutôt de choisir le mois en choisissant un jour dans un tableau JS.</li>
	<li>- L'effectif_total calculé par mois est erroné&nbsp;: Il ne tient pas compte des dates de périodes (trimestres,...)<br />
	Pour obtenir une valeur correcte, il faudrait enregistrer dans la table eleves une date d'entrée dans l'établissement.<br />
	Le champ date_entree existe dans le ElevesSansAdresse.xml; il faudra l'importer dans init_xml2, le prendre en compte dans modify_eleve et maj_import3</li>
	<li>- L'extraction des nombres d'élèves dépassant tant de demi-journée,... nécessite le remplissage de la table d'agrégation.</li>
	<li>- Entre la version 1.6.2 et la version 1.6.3 de Gepi, le type du champ MEF_CODE a changé.<br />
	Cela peut impliquer de re-remplir les MEF et de faire une mise à jour d'après Sconet pour importer les MEF associés aux élèves.<br />
	Sans cela, il se peut que vos totaux apparaissent à zéro.</li>
</ul>
</div>";
	require_once("../lib/footer.inc.php");
	die();
}

echo "<h2>Extraction statistique</h2>
<p>";
if(($mois==1)) {
	echo "<a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=12' title='Mois précédent'><img src='../images/icons/back.png' width='16' height='16' /></a> | ";
}
else {
	echo "<a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=".($mois-1)."' title='Mois précédent'><img src='../images/icons/back.png' width='16' height='16' /></a> | ";
}
echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir un autre mois</a>";
if(($mois==12)) {
	echo " | <a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=1' title='Mois suivant'><img src='../images/icons/forward.png' width='16' height='16' /></a>";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."?annee=".$annee."&amp;mois=".($mois+1)."' title='Mois suivant'><img src='../images/icons/forward.png' width='16' height='16' /></a>";
}
echo "</p>

<p class='bold'>Tableau \$tab_stat extrait pour le mois de ".$tab_stat['mois']."&nbsp;:</p>";
/*
echo "
<pre>";
print_r($tab_stat);
echo "</pre>";
*/
echo "<table class='boireaus boireaus_alt sortable resizable'>
	<thead>
		<tr>
			<th colspan='3'>MEF</th>
			<th colspan='3'>Absences non justifiées</th>
		</tr>
		<tr>
			<th class='text'>Libellé court</th>
			<th class='text'>Libellé long</th>
			<th class='text'>Libellé édition</th>
			<th class='number'>
				nj_sup_egal_4
			</th>
			<th class='number'>
				nj_4_a_10
			</th>
			<th class='number'>
				nj_sup_egal_11
			</th>
		</tr>
	<thead>
	<tbody>";
for($loop=0;$loop<count($tab_stat['mef']);$loop++) {
	echo "
		<tr>
			<td>".$tab_stat['mef'][$loop]['libelle_court']."</td>
			<td>".$tab_stat['mef'][$loop]['libelle_long']."</td>
			<td>".$tab_stat['mef'][$loop]['libelle_edition']."</td>
			<td>
				".$tab_stat['mef'][$loop]['nj_sup_egal_4']."
			</td>
			<td>
				".$tab_stat['mef'][$loop]['nj_4_a_10']."
			</td>
			<td>
				".$tab_stat['mef'][$loop]['nj_sup_egal_11']."
			</td>
		</tr>";
}
echo "
	</tbody>
</table>";

echo "</div>";

require_once("../lib/footer.inc.php");
?>
