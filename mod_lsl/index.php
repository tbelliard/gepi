<?php
/*
* Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_lsl/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_lsl/index.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Index du module LSL',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================


//=========================================================

// Création des tables

$sql="CREATE TABLE IF NOT EXISTS lsl_nomenclature (
id int(11) NOT NULL AUTO_INCREMENT,
code_mef VARCHAR(20) NOT NULL,
regroupement VARCHAR(100) NOT NULL,
discipline_livret VARCHAR(200) NOT NULL,
code_matiere_BCN VARCHAR(10) NOT NULL,
matiere_BCN VARCHAR(200) NOT NULL,
modalite_election_BCN VARCHAR(10) NOT NULL,
type_enseignement_suivi VARCHAR(100) NOT NULL,
notation_lsl VARCHAR(100) NOT NULL,
appreciation_lsl VARCHAR(100) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="SELECT 1=1 FROM lsl_nomenclature;";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if((mysqli_num_rows($test)==0)||(isset($_GET['reremplir_lsl_nomenclature']))) {
	if(isset($_GET['reremplir_lsl_nomenclature'])) {
		check_token();
	}

	$sql="TRUNCATE lsl_nomenclature;";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='MATHÉMATIQUES',
	code_matiere_BCN='061300',
	matiere_BCN='MATHEMATIQUES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='SCIENCES PHYSIQUES ET CHIMIQUES',
	code_matiere_BCN='069400',
	matiere_BCN='SCIENCES PHYSIQUES ET CHIMIQUES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='FRANÇAIS',
	code_matiere_BCN='',
	matiere_BCN='FRANCAIS',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='HISTOIRE-GÉOGRAPHIE',
	code_matiere_BCN='040600',
	matiere_BCN='HISTOIRE-GEOGRAPHIE',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='ÉDUCATION PHYSIQUE ET SPORTIVE',
	code_matiere_BCN='100100',
	matiere_BCN='EDUCATION PHYSIQUE ET SPORTIVE',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);




	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements spécifiques à la série ST2S', 
	discipline_livret='SCIENCES ET TECHNIQUES SANITAIRES ET SOCIALES',
	code_matiere_BCN='307600',
	matiere_BCN='SCIENC. & TECHNIQ. SANITAIRES & SOCIALES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements spécifiques à la série ST2S', 
	discipline_livret='BIOLOGIE ET PHYSIOPATHOLOGIE HUMAINES',
	code_matiere_BCN='307700 ',
	matiere_BCN='BIOLOGIE & PHYSIOPATHOLOGIE HUMAINES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements spécifiques à la série ST2S', 
	discipline_livret='ACTIVITÉS INTER-DISCIPLINAIRES',
	code_matiere_BCN='007500',
	matiere_BCN='ACTIVITES INTER-DISCIPLINAIRES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);





	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ÉDUCATION PHYSIQUE ET SPORTIVE',
	code_matiere_BCN='100100',
	matiere_BCN='EDUCATION PHYSIQUE ET SPORTIVE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ARTS PLASTIQUES',
	code_matiere_BCN='090100',
	matiere_BCN='ARTS PLASTIQUES',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='CINÉMA-AUDIOVISUEL',
	code_matiere_BCN='285200',
	matiere_BCN='CINEMA-AUDIOVISUEL',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='DANSE',
	code_matiere_BCN='082500',
	matiere_BCN='DANSE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='HISTOIRE DES ARTS',
	code_matiere_BCN='275700',
	matiere_BCN='HISTOIRE DES ARTS',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='MUSIQUE',
	code_matiere_BCN='083200',
	matiere_BCN='MUSIQUE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='THÉÂTRE',
	code_matiere_BCN='278500',
	matiere_BCN='THEATRE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ATELIER ARTISTIQUE',
	code_matiere_BCN='278000',
	matiere_BCN='ATELIER ARTISTIQUE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ÉDUCATION PHYSIQUE ET SPORTIVE DE COMPLÉMENT',
	code_matiere_BCN='104100',
	matiere_BCN='EDUC. PHYSIQUE ET SPORTIVE DE COMPLEMENT',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);




	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires de langues vivantes', 
	discipline_livret='LV1',
	code_matiere_BCN='030001*',
	matiere_BCN='LANGUE VIVANTE 1',
	modalite_election_BCN='O',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21133104110',
	regroupement='Enseignements obligatoires de langues vivantes', 
	discipline_livret='LV2',
	code_matiere_BCN='030002*',
	matiere_BCN='LANGUE VIVANTE 2',
	modalite_election_BCN='O',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);




	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='PHILOSOPHIE',
	code_matiere_BCN='013000',
	matiere_BCN='PHILOSOPHIE',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='ÉDUCATION PHYSIQUE ET SPORTIVE',
	code_matiere_BCN='100100',
	matiere_BCN='EDUCATION PHYSIQUE ET SPORTIVE',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='MATHÉMATIQUES',
	code_matiere_BCN='061300',
	matiere_BCN='MATHEMATIQUES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='SCIENCES PHYSIQUES ET CHIMIQUES',
	code_matiere_BCN='069400',
	matiere_BCN='SCIENCES PHYSIQUES ET CHIMIQUES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires', 
	discipline_livret='HISTOIRE-GÉOGRAPHIE',
	code_matiere_BCN='040600',
	matiere_BCN='HISTOIRE-GEOGRAPHIE',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);




	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements spécifiques à la série ST2S', 
	discipline_livret='SCIENCES ET TECHNIQUES SANITAIRES ET SOCIALES',
	code_matiere_BCN='307600',
	matiere_BCN='SCIENC. & TECHNIQ. SANITAIRES & SOCIALES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements spécifiques à la série ST2S', 
	discipline_livret='BIOLOGIE ET PHYSIOPATHOLOGIE HUMAINES',
	code_matiere_BCN='307700 ',
	matiere_BCN='BIOLOGIE & PHYSIOPATHOLOGIE HUMAINES',
	modalite_election_BCN='S',
	type_enseignement_suivi='Obligatoire',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);




	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ÉDUCATION PHYSIQUE ET SPORTIVE',
	code_matiere_BCN='100100',
	matiere_BCN='EDUCATION PHYSIQUE ET SPORTIVE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ARTS PLASTIQUES',
	code_matiere_BCN='090100',
	matiere_BCN='ARTS PLASTIQUES',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='CINÉMA-AUDIOVISUEL',
	code_matiere_BCN='285200',
	matiere_BCN='CINEMA-AUDIOVISUEL',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='DANSE',
	code_matiere_BCN='082500',
	matiere_BCN='DANSE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='HISTOIRE DES ARTS',
	code_matiere_BCN='275700',
	matiere_BCN='HISTOIRE DES ARTS',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='MUSIQUE',
	code_matiere_BCN='083200',
	matiere_BCN='MUSIQUE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='THÉÂTRE',
	code_matiere_BCN='278500',
	matiere_BCN='THEATRE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ATELIER ARTISTIQUE',
	code_matiere_BCN='278000',
	matiere_BCN='ATELIER ARTISTIQUE',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements facultatifs', 
	discipline_livret='ÉDUCATION PHYSIQUE ET SPORTIVE DE COMPLÉMENT',
	code_matiere_BCN='104100',
	matiere_BCN='EDUC. PHYSIQUE ET SPORTIVE DE COMPLEMENT',
	modalite_election_BCN='F',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);



	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires de langues vivantes', 
	discipline_livret='LV1',
	code_matiere_BCN='030001*',
	matiere_BCN='LANGUE VIVANTE 1',
	modalite_election_BCN='O',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="INSERT INTO lsl_nomenclature SET
	code_mef='21233104110',
	regroupement='Enseignements obligatoires de langues vivantes', 
	discipline_livret='LV2',
	code_matiere_BCN='030002*',
	matiere_BCN='LANGUE VIVANTE 2',
	modalite_election_BCN='O',
	type_enseignement_suivi='0_a_2',
	notation_lsl='Obligatoire',
	appreciation_lsl='Obligatoire';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$msg="Table Nomenclatures LSL re-remplie.<br />";
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

//**************** EN-TETE *****************
$titre_page = "Livret Scolaire Lycée";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

debug_var();

echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
echo " | <a href='".$_SERVER['PHP_SELF']."?reremplir_lsl_nomenclature=y".add_token_in_url()."'>Re-remplir la table Nomenclatures LSL</a>";

/*
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form_creer_projet'>\n";
	echo "Créer un nouveau projet&nbsp;: </td><td style='text-align:left;'><input type='text' name='projet' value='' /> <input type='submit' name='creer_projet' value='Créer' />\n";
	echo "</form>\n";
*/

$sql="SELECT DISTINCT l.code_mef FROM lsl_nomenclature l;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "</p>";
	echo "<p style='color:red'>Aucune nomenclature LSL n'est enregistrée.</p>";
	require("../lib/footer.inc.php");
	die();
}

$tab_mef_lsl=array();
while($lig=mysqli_fetch_object($res)) {
	$tab_mef_lsl[]=$lig->code_mef;
}

$tab_mef=get_tab_mef();

if(!isset($id_classe)) {
	echo "</p>";

	$tab_annees=array();
	$sql="SELECT DISTINCT annee FROM archivage_disciplines ORDER BY annee DESC;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucune année n'est archivée.</p>";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			$tab_annees[]=$lig->annee;
		}

		echo "
<p class='bold'>Archivages correspondant aux MEF du LSL&nbsp;:</p>
<table class='boireaus boireaus_alt resizable sortable'>
	<thead>
		<tr>
			<th class='text' title=\"Cliquez pour trier.\">Code</th>
			<th class='text' title=\"Cliquez pour trier.\">Mef</th>";
		for($loop=0;$loop<count($tab_annees);$loop++) {
			echo "
			<th class='text' title=\"Effectif de l'année.\">".$tab_annees[$loop]."</th>";
		}
		echo "
		</tr>
	</thead>
	<tbody>";

		foreach($tab_mef as $code_mef => $current_tab_mef) {
			if(in_array($code_mef, $tab_mef_lsl)) {
				echo "
		<tr>
			<td>".$code_mef."</td>
			<td>".$current_tab_mef['designation_courte']."</td>";
				for($loop=0;$loop<count($tab_annees);$loop++) {
					echo "
			<td>";
					$sql="SELECT DISTINCT INE FROM archivage_disciplines WHERE annee='".$tab_annees[$loop]."' AND mef_code='".$code_mef."';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					echo mysqli_num_rows($res);
					echo "</td>";
				}
				echo "
		</tr>";
			}
		}

		echo "
	</tbody>
</table>";
	}

	echo "<br /><p class='bold'>Choix des classes&nbsp;:</p>\n";

	// Liste des classes avec élève:
	$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
	$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_classes=mysqli_num_rows($call_classes);
	if($nb_classes==0){
		echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_txt=array();
	$tab_nom_champ=array();
	$tab_id_champ=array();
	$tab_valeur_champ=array();
	$cpt=0;
	while($lig_clas=mysqli_fetch_object($call_classes)) {
		$tab_txt[]=$lig_clas->classe;
		$tab_nom_champ[]="id_classe[]";
		$tab_id_champ[]="tab_id_classe_".$cpt;
		$tab_valeur_champ[]=$lig_clas->id;
		$cpt++;
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	echo tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, "checkbox_change");

	echo "
	<p><input type='submit' value='Valider' /></p>\n";
	echo "</form>\n";

	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a></p>";

if(!isset($login_eleve)) {

	echo "<br /><p class='bold'>Choix des élèves&nbsp;:</p>\n";

	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	for($loop=0;$loop<count($id_classe);$loop++) {
		echo "<p class='bold' style='margin-top:1em;'>Classe ".get_nom_classe($id_classe[$loop])."</p>
<input type='hidden' name='id_classe[]' value='".$id_classe[$loop]."' />";
		echo liste_checkbox_eleves_classe2($id_classe[$loop], "", array(), 'login_eleve', 'id_classe_'.$loop.'_login_eleve', 'cocher_decocher_'.$loop);
	}

	echo "
	<div id='fixe'><input type='submit' value='Valider' /></div>\
	<p><input type='submit' value='Valider' /></p>

	<p style='text-indent:-4em; margin-left:4em; margin-top:1em; color:red'><em>NOTE&nbsp;:</em> A FAIRE : Ne pas proposer l'extraction pour des élèves dont le MEF n'est pas dans la table 'lsl_nomenclature'.</p>
</form>\n";

	require("../lib/footer.inc.php");
	die();
}







function get_chaine_avisInvestissement($ine, $annee) {
	// A FAIRE : Pouvoir saisir l'avisInvestissement... et le stocker dans une table
	$avisInvestissement="";
	$nomInvestissement="";
	$prenomInvestissement="";
	$dateInvestissement="aaaa-mm-jj";

	$retour='
					<avisInvestissement date="'.$dateInvestissement.'" nom="'.$nomInvestissement.'" prenom="'.$prenomInvestissement.'">'.$avisInvestissement.'</avisInvestissement>';

	// Pour le moment les infos ne sont pas récupérées
	$retour="";

	return $retour;
}


function get_chaine_avisExamen($ine) {
	// A FAIRE : Pouvoir saisir l'avisExamen... et le stocker dans une table
	$avisExamen="T";

	$retour='
			<avisExamen code="'.$avisExamen.'"/>';

	// Pour le moment les infos ne sont pas récupérées
	$retour="";

	return $retour;
}

function get_chaine_engagements($ine) {
	// Faut-il restreindre aux années de 1ère et terminale la recherche des engagements?
	$chaine_engagements="";
	$sql="SELECT DISTINCT code_engagement FROM archivage_engagements WHERE INE='".$ine."' AND code_engagement!='';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$chaine_engagements.='
			<engagement code="'.$lig->code_engagement.'"/>';
		}
	}
	$sql="SELECT DISTINCT code_engagement FROM archivage_engagements WHERE INE='".$ine."' AND code_engagement='';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$chaine_engagements.='
			<engagement-autre>"'.$lig->nom_engagement.'"</engagement-autre>';
		}
	}

	return $chaine_engagements;
}

/*
foreach($tab_mef as $code_mef => $current_tab_mef) {
	if(in_array($code_mef, $tab_mef_lsl)) {
		echo "
<h3>".$current_tab_mef['designation_courte']." ($code_mef)</h3>";

		for($loop=0;$loop<count($tab_annees);$loop++) {
		}

	}
}
*/

$gepiSchoolRne=getSettingValue("gepiSchoolRne");
if($gepiSchoolRne=="") {
	echo "<p style='color:red'>Le RNE de l'établissement n'est pas renseigné.<br />Le fichier produit ne sera pas valide.</p>";
}

// Debut du fichier
$xml='<?xml version="1.0" encoding="UTF-8"?>
<!--Sample XML file generated by XMLSPY v2004 rel. 3 U (http://www.xmlspy.com)-->
<lsl:lsl xmlns:lsl="urn:ac-grenoble.fr:lsl:import" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ac-grenoble.fr:lsl:import
D:\dev\projects\lsnlycee\workspace\trunk\lsnlycee-webapp\src\main\resources\xsd\import-lsl.xsd" schemaVersion="1.0">
	<entete>
		<editeur>GEPI</editeur>
		<application>GEPI</application>
		<etablissement>'.$gepiSchoolRne.'</etablissement>
	</entete>
	<donnees>';

// A FAIRE/VOIR : Cas des élèves changeant de classe
//                Il faut qu'on obtienne la même extraction pour un tel élève, quelle que soit la classe source.
$tab_deja_extrait=array();
for($loop_ele=0;$loop_ele<count($login_eleve);$loop_ele++) {
	$poursuivre="y";
	if(in_array($login_eleve[$loop_ele], $tab_deja_extrait)) {
		$poursuivre="n";
	}

	if($poursuivre=="y") {
		$infos_eleve=get_info_eleve($login_eleve[$loop_ele], "");

		if(count($infos_eleve)==0) {
			$poursuivre="n";
		}
		/*
		// Test pour un élève
		$current_eleve_INE="XXXXXXXXXX";
		$current_ele_id="517818";
		// L'ele_id est requis.
		// Il faut donc faire la sélection des élèves sur la base courante
		// Tableau des classes, sélection des élèves, afficher les élèves en erreur,...

		$sql="SELECT * FROM archivage_eleves WHERE INE='".$current_eleve_INE."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>ERREUR : L'élève ".$current_eleve_INE." n'a pas été trouvé dans la table archivage_eleves</span><br />";
		}
		else {
			$lig=mysqli_fetch_assoc($res);
			$infos_eleve=$lig;
		}
		*/
	}

	if($poursuivre=="y") {

		$current_eleve_INE=$infos_eleve['no_gep'];
		$current_ele_id=$infos_eleve['ele_id'];

		// A FAIRE : Stocker le type de période en fonction du MEF (Trimestre ou Semestre)
		$codePeriode="T";

		$xml_ele='
		<eleve id="'.$current_ele_id.'">
			<engagements>'.get_chaine_engagements($current_eleve_INE).'
			</engagements>'.get_chaine_avisExamen($current_eleve_INE).'
			<scolarites>';

		// Boucler sur les années dont les mef de l'élève sont dans $tab_mef_lsl
		$sql="SELECT DISTINCT annee, mef_code FROM archivage_disciplines ad, lsl_nomenclature ln WHERE INE='".$current_eleve_INE."' AND ln.code_mef=ad.mef_code ORDER BY annee;";
		$res_annees=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_annees)>0) {
			while($lig_annee=mysqli_fetch_object($res_annees)) {

				// A FAIRE : Pouvoir choisir le nom de l'année
				// En principe dans les tables d'archivage c'est au format 2013/2014, mais on pourrait avoir 2013-2014 ou tout autre chose puisque le champ est libre lors de l'archivage
				$annee=substr($lig_annee->annee,0,4);



				$xml_ele.='
				<scolarite annee-scolaire="'.$annee.'" code-periode="'.$codePeriode.'">';
				$xml_ele.=get_chaine_avisInvestissement($current_eleve_INE, $annee);


				// Boucler sur les enseignements de lsl_nomenclature... en tenant compte de ce qui est obligatoire ou non
				$sql="SELECT * FROM lsl_nomenclature ln WHERE ln.code_mef='".$lig_annee->mef_code."';";
				//echo "$sql<br />";
				$res_lsl=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_lsl)>0) {
					while($lig_lsl=mysqli_fetch_object($res_lsl)) {

						$current_code_matiere=$lig_lsl->code_matiere_BCN;
						//echo "\$current_code_matiere='$current_code_matiere'<br />";
						// Cas particulier des LV1 et LV2
						if(preg_match("/\*$/", $current_code_matiere)) {
							//if("$current_code_matiere"=="030001 *") {
							if("$current_code_matiere"=="030001*") {
								$sql="SELECT DISTINCT nv.code FROM nomenclatures_valeurs nv, 
														archivage_disciplines ad 
													WHERE nv.valeur LIKE '%LV1' AND 
														nv.code=ad.code_matiere AND 
														mef_code='".$lig_annee->mef_code."' AND 
														annee='".$lig_annee->annee."' AND 
														INE='".$current_eleve_INE."';";
								$lv="LV1";
							}
							else {
								$sql="SELECT DISTINCT nv.code FROM nomenclatures_valeurs nv, 
														archivage_disciplines ad 
													WHERE nv.valeur LIKE '%LV2' AND 
														nv.code=ad.code_matiere AND 
														mef_code='".$lig_annee->mef_code."' AND 
														annee='".$lig_annee->annee."' AND 
														INE='".$current_eleve_INE."';";
								$lv="LV2";
							}
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)==0) {
								// On ne va pas trouver d'enregistrement avec un code_matiere contenant *
							}
							elseif(mysqli_num_rows($res)>1) {
								echo "<span style='color:red'>ERREUR : ".mysqli_num_rows($res)." $lv trouvées dans archivage_disciplines pour ".$infos_eleve['nom']." ".$infos_eleve['prenom']." ($current_eleve_INE) ($lig_annee->annee) en $lig_lsl->discipline_livret ($current_code_matiere)</span><br />";
							}
							else {
								$lig=mysqli_fetch_object($res);
								$current_code_matiere=$lig->code;
							}
						}

						// Récupérer les infos archivées pour la matière courante
						$sql="SELECT * FROM archivage_disciplines WHERE mef_code='".$lig_annee->mef_code."' AND 
														annee='".$lig_annee->annee."' AND 
														INE='".$current_eleve_INE."' AND 
														code_matiere='".$current_code_matiere."' 
													ORDER BY num_periode;";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)==0) {
							if($lig_lsl->modalite_election_BCN!='F') {
								echo "<span style='color:red'>ERREUR : Pas d'enregistrement dans archivage_disciplines pour ".$infos_eleve['nom']." ".$infos_eleve['prenom']." ($current_eleve_INE) ($lig_annee->annee) en $lig_lsl->discipline_livret ($current_code_matiere)</span><br />";
							}
						}
						/*
						// Il va y avoir un enregistrement par période
						elseif(mysqli_num_rows($res)>1) {
							echo "<span style='color:red'>ANOMALIE : ".mysqli_num_rows($res)." enregistrements dans archivage_disciplines pour ".$infos_eleve['nom']." ".$infos_eleve['prenom']." ($current_eleve_INE) ($lig_annee->annee) en $lig_lsl->discipline_livret ($current_code_matiere)</span><br />";
						}
						*/
						else {
							// Première période
							$lig=mysqli_fetch_object($res);
/*
echo "<pre>";
print_r($lig);
echo "</pre>";
*/
							$chaine_structure_grp='
						<structure effectif="'.$lig->effectif.'"/>';
							if($lig->id_groupe!=0) {
								// mef_code='".$lig_annee->mef_code."' 
								// Il risque d'y avoir plusieurs MEF suivant un même enseignement
								$sql="SELECT * FROM archivage_disciplines WHERE special='GRP_ANNEE' AND 
														id_groupe='$lig->id_groupe' AND 
														annee='".$lig_annee->annee."' AND 
														code_matiere='".$current_code_matiere."';";
								//echo "$sql<br />";
								$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_grp)>0) {
									$lig_grp=mysqli_fetch_object($res_grp);
									$chaine_structure_grp='
						<structure effectif="'.$lig_grp->effectif.'" moyenne="'.sprintf("%02.02f", $lig_grp->moyclasse).'" repar-moins-huit="'.sprintf("%02.02f", $lig_grp->repar_moins_8).'" repar-huit-douze="'.sprintf("%02.02f", $lig_grp->repar_8_12).'" repar-plus-douze="'.sprintf("%02.02f", $lig_grp->repar_plus_12).'"/>';
								}
							}
/*
echo "<pre>";
echo htmlentities($chaine_structure_grp);
echo "</pre>";
*/

							$attribut_optionnel_date="";
							//$attribut_optionnel_date=' date="2014-05-13"';
 							$xml_ele.='
					<evaluation modalite-election="'.$lig_lsl->modalite_election_BCN.'" code-matiere="'.$current_code_matiere.'"'.$attribut_optionnel_date.'>';
							$xml_ele.=$chaine_structure_grp;

							$xml_ele.='
						<periodiques>
							<periode numero="'.$lig->num_periode.'" moyenne="'.sprintf("%02.02f", $lig->note).'"/>';

							// Périodes suivantes
							while($lig=mysqli_fetch_object($res)) {
								$xml_ele.='
							<periode numero="'.$lig->num_periode.'" moyenne="'.sprintf("%02.02f", $lig->note).'"/>';
							}

							$xml_ele.='
						</periodiques>';

						}



					}
				}

				$xml_ele.='
				</scolarite>';

			}
		}

		// Fin élève
		$xml_ele.='
			</scolarites>
		</eleve>';

		// A FAIRE : Ne pas concaténer $xml_ele s'il y a une erreur sur l'extraction pour l'élève
		$xml.=$xml_ele;

		$tab_deja_extrait[]=$login_eleve[$loop_ele];

	}
}

		// Fin du fichier
		$xml.='
	</donnees>
</lsl:lsl>';

		// Affichage
		echo "<pre>";
		echo htmlentities($xml);
		echo "</pre>";




echo "<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Ce module nécessite l'archivage des données de l'année dans le module Années antérieures.<br />
	C'est là que l'on recherche l'essentiel de la partie évaluations du livret, les engagements,...</li>
	<li>
		Il manque&nbsp;:<br />
		<ul>
			<li>
				La gestion des compétences.<br />
				C'est un peu vague pour le moment.<br />
				La liste des compétences est-elle la même pour tous les MEF?<br />
				Quand ces compétences sont elles remplies au lycée?<br />
				Les professeurs piochent dans les compétences?<br />
				Comment cela se passe-t-il sur le livrets scolaire papier?<br />
				Si une même compétence est saisie par un enseignant et un autre, apparait-elle dans les deux sections de matière?<br />
				Quand il est indiqué au maximum tant de compétences, comment les choisit-on s'il y en a plus de validé?<br />
				Ou: La validation ne se fait que sur le livret, donc on ne propose pas plus de saisies de compétences que le max autorisé?<br />
				...
			</li>
			<li>
				La possibilité de saisir un avisInvestissement, un avisEngagement, un avisChefEtab... et un avis annuel par matière
			</li>
			<li>Possibilité de saisir les engagements pour l'année 2013/2014 directement dans archivage_engagements pour l'année de 1ère?</li>
			<li>Prendre en compte les nombre max d'enseignements facultatifs.</li>
		</ul>
	</li>
	<li>Quand l'extraction a-t-elle lieu?<br />
	Chaque année de 1ère et terminale?<br />
	Seulement en fin de terminale et lors d'un changement d'établissement?</li>
	<li>Dans la nomenclature (LSL_nomenclature_v1.1.xlsx), l'EPS est dans les Enseignements obligatoires et dans les Enseignements facultatifs avec le même code 100100.<br />
	(sans compter ÉDUCATION PHYSIQUE ET SPORTIVE DE COMPLÉMENT (104100 ))<br />
	Il peut y avoir deux enseignements d'EPS et deux notes différentes pour un élève?</li>
	<li>Vérifier comment taguer les langues en LV1/LV2 pour les élèves.<br />
	On peut passer en revue tous les code associés mais bon...</li>
	<li>Faut-il restreindre aux années de 1ère et terminale la recherche des engagements?</li>
	<li>Faire le tour des 'A FAIRE' dans le code</li>
</ul>";

require("../lib/footer.inc.php");
?>
