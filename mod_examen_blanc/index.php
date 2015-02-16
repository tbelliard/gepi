<?php
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_examen_blanc/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_examen_blanc/index.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Examen blanc: Accueil',
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

include('lib_exb.php');

//=========================================================

/*

// Création des tables

$sql="CREATE TABLE IF NOT EXISTS ex_examens (
id int(11) unsigned NOT NULL auto_increment,
intitule VARCHAR( 255 ) NOT NULL ,
description TEXT NOT NULL ,
date DATE NOT NULL default '0000-00-00',
etat VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS ex_matieres (
id int(11) unsigned NOT NULL auto_increment,
id_exam int(11) unsigned NOT NULL,
matiere VARCHAR( 255 ) NOT NULL ,
coef DECIMAL(3,1) NOT NULL default '1.0',
bonus CHAR(1) NOT NULL DEFAULT 'n',
ordre INT(11) unsigned NOT NULL,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS ex_classes (
id int(11) unsigned NOT NULL auto_increment,
id_exam int(11) unsigned NOT NULL,
id_classe int(11) unsigned NOT NULL,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS ex_groupes (
id int(11) unsigned NOT NULL auto_increment,
id_exam int(11) unsigned NOT NULL,
matiere varchar(50) NOT NULL,
id_groupe int(11) unsigned NOT NULL,
type VARCHAR( 255 ) NOT NULL ,
id_dev int(11) NOT NULL DEFAULT '0',
valeur VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
);";
$create_table=mysql_query($sql);

$sql="CREATE TABLE IF NOT EXISTS ex_notes (
id int(11) unsigned NOT NULL auto_increment,
id_ex_grp int(11) unsigned NOT NULL,
login VARCHAR(255) NOT NULL default '',
note float(10,1) NOT NULL default '0.0',
statut varchar(4) NOT NULL default '',
PRIMARY KEY ( id )
);";
//echo "$sql<br />";
$create_table=mysql_query($sql);


//=========================================================
// A TRANSFERER VERS utilitaires/updates/152_to_153.inc.php et sql/structure_gepi.sql
$result="";
$result.="&nbsp;->Ajout d'un champ 'valeur' à la table 'ex_groupes'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ex_groupes LIKE 'valeur';"));
if ($test_champ>0) {
	$result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
}
else {
	$query = mysql_query("ALTER TABLE ex_groupes ADD valeur VARCHAR(255) NOT NULL;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}

	echo $result;
}
//=========================================================
*/

//debug_var();

//=========================================================

$id_exam=isset($_POST['id_exam']) ? $_POST['id_exam'] : (isset($_GET['id_exam']) ? $_GET['id_exam'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

//$modif_exam=isset($_POST['modif_exam']) ? $_POST['modif_exam'] : (isset($_GET['modif_exam']) ? $_GET['modif_exam'] : NULL);

$acces_mod_exb_prof="n";
if($_SESSION['statut']=='professeur') {

	if(!is_pp($_SESSION['login'])) {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if(getSettingValue('modExbPP')!='yes') {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if((isset($id_exam))&&(!is_pp_proprio_exb($id_exam))) {
		header("Location: ../accueil.php?msg=".rawurlencode("Vous n'êtes pas propriétaire de l'examen blanc n°$id_exam."));
		die();
	}

	$acces_mod_exb_prof="y";
}

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=='y')) {

	if(isset($id_exam)) {
		$msg="";

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'examen choisi (<i>$id_exam</i>) n'existe pas.\n";
		}
		else {
			$lig=mysqli_fetch_object($res);
			//$etat=$lig->etat;
		
			/*
			if($etat=='clos') {
				if(isset($_POST['modif_exam'])) {unset($_POST['modif_exam']);}
				if((isset($mode))&&($mode!='clore')&&($mode!='declore')&&($mode!='modif_exam')) {$mode=NULL;}
			}
			*/
		}
	}

	// Témoin d'une modification de numéros anonymat (pour informer qu'il faut regénérer les étiquettes,...)
	//$temoin_n_anonymat='n';
	// Témoin d'une erreur anonymat pour un élève au moins
	//$temoin_erreur_n_anonymat='n';

	//if(isset($_POST['creer_epreuve'])) {
	if((isset($_POST['creer_exam']))||(isset($_POST['modif_exam']))) {
		// Correction, modification des paramètres d'un examen

		check_token();

		$intitule=isset($_POST['intitule']) ? $_POST['intitule'] : "Examen blanc";
		$date=isset($_POST['date']) ? $_POST['date'] : "";
		$description=isset($_POST['description']) ? $_POST['description'] : "";
		//$type_anonymat=isset($_POST['type_anonymat']) ? $_POST['type_anonymat'] : "ele_id";

		if(mb_strlen(preg_replace("/[A-Za-z0-9 _\.-]/","",remplace_accents($intitule,'all')))!=0) {$intitule=preg_replace("/[^A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸0-9_\.-]/"," ",$intitule);}
		if($intitule=="") {$intitule="Examen blanc";}

		//$tab_anonymat=array('elenoet','ele_id','no_gep','alea');
		//if(!in_array($type_anonymat,$tab_anonymat)) {$type_anonymat="ele_id";}

		if (isset($NON_PROTECT["description"])){
			$description=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["description"]));
		}
		else {
			$description="";
		}

		$tab=explode("/",$date);
		if(checkdate($tab[1],$tab[0],$tab[2])) {
			$date=$tab[2]."-".$tab[1]."-".$tab[0];
		}
		else {
			$date="0000-00-00";
		}

		if(!isset($id_exam)) {
			//$sql="INSERT INTO eb_epreuves SET intitule='$intitule', description='".addslashes($description)."', type_anonymat='$type_anonymat', date='', etat='';";
			$sql="INSERT INTO ex_examens SET intitule='$intitule', description='$description', date='$date';";
			if($insert=mysqli_query($GLOBALS["mysqli"], $sql)) {
				$id_exam=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
				$msg="Examen n°$id_exam : '$intitule' créé.<br />";
			}
			else {
				$msg="ERREUR lors de la création de l'examen '$intitule'.<br />";
				//$msg.="<br />$sql";
			}
		}
		else {

			$sql="UPDATE ex_examens SET intitule='$intitule', description='$description', date='$date' WHERE id='$id_exam';";
			if($update=mysqli_query($GLOBALS["mysqli"], $sql)) {
				$msg="Examen n°$id_exam: '$intitule' mise à jour.";
			}
			else {
				$msg="ERREUR lors de la modification de l'examen '$intitule'.";
				//$msg.="<br />$sql";
			}
		}
		$mode="modif_exam";
	}
	elseif((isset($id_exam))&&($mode=='modif_coef')) {
		check_token();

		$tab_matiere=isset($_POST['tab_matiere']) ? $_POST['tab_matiere'] : array();
		$bonus=isset($_POST['bonus']) ? $_POST['bonus'] : array();
		$coef=isset($_POST['coef']) ? $_POST['coef'] : array();

		$nb_reg=0;
		for($i=0;$i<count($tab_matiere);$i++) {
			if(isset($coef[$i])) {
				$enregistrer='y';
				if(preg_match("/[^0-9\.]/",$coef[$i])) {
					$msg.="$coef[$i] contient des caractères non numériques.<br />\n";
					$enregistrer='n';
				}
				elseif(mb_strlen(preg_replace("/[^\.]/","",$coef[$i]))>1) {
					$msg.="Il y a plusieurs POINTS dans $coef[$i]<br />\n";
					$enregistrer='n';
				}

				if($enregistrer=='y') {
					$sql="UPDATE ex_matieres SET coef='$coef[$i]'";
					if(isset($bonus[$i])) {
						$sql.=", bonus='y'";
					}
					else {
						$sql.=", bonus='n'";
					}
		
					$sql.=" WHERE id_exam='$id_exam' AND matiere='$tab_matiere[$i]';";
					//echo "$sql<br />\n";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {$nb_reg++;}
				}
			}
		}
		if($nb_reg>0) {$msg.="Coefficients et bonus mis à jour.<br />";}

		$mode="modif_exam";
	}
	elseif((isset($id_exam))&&($mode=='suppr_exam')) {
		check_token();

		// Suppression d'un examen
		//echo "gloups";
		//$tab_tables=array('ex_notes', 'ex_groupes', 'ex_matieres', 'ex_classes', 'ex_examens');
		//$tab_tables=array('ex_notes', 'ex_groupes', 'ex_matieres', 'ex_classes');
		$tab_tables=array('ex_groupes', 'ex_matieres', 'ex_classes');
		// A REVOIR: COMMENT VIRER LES NOTES SAISIES HORS devoirs/épreuves classiques
		for($i=0;$i<count($tab_tables);$i++) {
			$sql="DELETE FROM $tab_tables[$i] WHERE id_exam='$id_exam';";
			//echo "$sql<br />";
			$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$suppr) {
				$msg="ERREUR lors de la suppression de l'examen $id_exam";
				//for($j=0;$j<$i;$j++) {$msg.=""}
				unset($id_exam);
				unset($mode);
				break;
			}
		}

		if($msg=='') {
			$sql="DELETE FROM ex_examens WHERE id='$id_exam';";
			$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$suppr) {
				$msg="ERREUR lors de la suppression de l'examen $id_exam";
			}
			else {
				$msg="Suppression de l'examen $id_exam effectuée.";
			}
		}
		unset($id_exam);
		unset($mode);
	}
	elseif((isset($id_exam))&&($mode=='ajout_classes')) {
		check_token();

		// Ajout de classes pour l'examen sélectionné
		$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : array());

		// On contrôle en cas d'accès prof qu'il est bien PP de ces classes
		if($_SESSION['statut']=='professeur') {
			for($i=0;$i<count($id_classe);$i++) {
				if(!is_pp($_SESSION['login'], $id_classe[$i])) {
					$gepi_prof_suivi=retourne__denomination_pp($id_classe[$i]);
					header("Location: ".$_SERVER['PHP_SELF']."?id_exam=$id_exam&msg=".rawurlencode("Vous n'êtes pas ".$gepi_prof_suivi." dans la classe de ".get_class_from_id($id_classe[$i])));
					die();
				}
			}
		}

		$nb_classes_supprimees=0;
		$tab_classes_assoc_old=array();
		$sql="SELECT DISTINCT id_classe FROM ex_classes WHERE id_exam='$id_exam';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$tab_classes_assoc_old[]=$lig->id_classe;

			if(!in_array($lig->id_classe,$id_classe)) {

				// Les groupes associés à la classe sont ils encore associés à une autre classe de l'examen?
				$sql="SELECT DISTINCT eg.id_groupe FROM j_groupes_classes jgc, ex_groupes eg WHERE eg.id_exam='$id_exam' AND jgc.id_classe='$lig->id_classe' AND jgc.id_groupe=eg.id_groupe;";
				//echo "$sql<br />";
				$res1=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig1=mysqli_fetch_object($res1)) {
					$sql="SELECT 1=1 FROM j_groupes_classes jgc, ex_groupes eg WHERE jgc.id_classe!='$lig->id_classe' AND jgc.id_groupe=eg.id_groupe AND eg.id_groupe='$lig1->id_groupe' AND eg.id_exam='$id_exam';";
					//echo "$sql<br />";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {
						$sql="DELETE FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='$lig1->id_groupe';";
						//echo "$sql<br />";
						$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
						// IL FAUDRAIT VIDER AUSSI ex_notes
					}
				}

				$sql="DELETE FROM ex_classes WHERE id_exam='$id_exam' AND id_classe='$lig->id_classe';";
				//echo "$sql<br />";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_classes_supprimees++;
			}
		}

		$nb_classes_ajoutees=0;
		for($i=0;$i<count($id_classe);$i++) {
			if(!in_array($id_classe[$i],$tab_classes_assoc_old)) {
				$sql="INSERT INTO ex_classes SET id_exam='$id_exam', id_classe='$id_classe[$i]';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if($insert) {$nb_classes_ajoutees++;}
			}
		}

		if($nb_classes_supprimees>0) {$msg.="$nb_classes_supprimees classe(s) supprimée(s) de l'examen $id_exam<br />";}
		if($nb_classes_ajoutees>0) {$msg.="$nb_classes_ajoutees classe(s) ajoutée(s) à l'examen $id_exam<br />";}

		$mode="modif_exam";

	}
	elseif((isset($id_exam))&&($mode=='ajout_matieres')) {
		check_token();

		// Ajout de matières pour l'examen sélectionné
		$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : array());

		$nb_matieres_supprimees=0;
		$tab_matieres_assoc_old=array();
		$sql="SELECT DISTINCT matiere FROM ex_matieres WHERE id_exam='$id_exam';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		while($lig=mysqli_fetch_object($res)) {
			$tab_matieres_assoc_old[]=$lig->matiere;

			if(!in_array($lig->matiere,$matiere)) {
				$sql="DELETE FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$lig->matiere';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);

				$sql="DELETE FROM ex_matieres WHERE id_exam='$id_exam' AND matiere='$lig->matiere';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_matieres_supprimees++;
			}
		}

		$nb_matieres_ajoutees=0;
		for($i=0;$i<count($matiere);$i++) {
			if(!in_array($matiere[$i],$tab_matieres_assoc_old)) {
				$sql="INSERT INTO ex_matieres SET id_exam='$id_exam', matiere='$matiere[$i]';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if($insert) {$nb_matieres_ajoutees++;}
			}
		}

		if($nb_matieres_supprimees>0) {$msg.="$nb_matieres_supprimees matière(s) supprimée(s) de l'examen $id_exam<br />";}
		if($nb_matieres_ajoutees>0) {$msg.="$nb_matieres_ajoutees matière(s) ajoutée(s) à l'examen $id_exam<br />";}

		$mode="modif_exam";

	}
	elseif((isset($id_exam))&&($mode=='ajout_groupes')) {
		check_token();

		// Ajout de groupes pour l'examen sélectionnée
		$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : array());
		$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : array());
		$groupe_hors_enseignement=isset($_POST['groupe_hors_enseignement']) ? $_POST['groupe_hors_enseignement'] : (isset($_GET['groupe_hors_enseignement']) ? $_GET['groupe_hors_enseignement'] : "n");

		// A FAIRE: Contrôler les caractères de $matiere

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'examen n°$id_exam n'existe pas.<br />";
		}
		else {
			$tab_id_groupe_assoc_old=array();
			$sql="SELECT id_groupe FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$matiere';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_suppr=0;
			while($lig=mysqli_fetch_object($res)) {
				$tab_id_groupe_assoc_old[]=$lig->id_groupe;

				//if(!in_array($lig->id_groupe,$id_groupe)) {
				if(!in_array($lig->id_groupe,$id_groupe)) {
					if($lig->id_groupe!=0) {
						$sql="DELETE FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$matiere' AND id_groupe='$lig->id_groupe';";
						$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
						$nb_suppr++;
					}
					elseif($groupe_hors_enseignement!='y') {
						$sql="DELETE FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$matiere' AND id_groupe='$lig->id_groupe';";
						$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
						$nb_suppr++;
					}
				}
			}
			if($nb_suppr>0) {$msg.="$nb_suppr association(s) de groupe(s) supprimée(s).<br />";}

			$nb_ajout=0;
			for($i=0;$i<count($id_groupe);$i++) {
				if(!in_array($id_groupe[$i],$tab_id_groupe_assoc_old)) {
					$sql="INSERT INTO ex_groupes SET id_exam='$id_exam', id_groupe='$id_groupe[$i]', matiere='$matiere';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout du groupe n°$id_groupe[$i]<br />";
					}
					else {$nb_ajout++;}
				}
				//if($msg=='') {$msg="Ajout de(s) groupe(s) effectué.<br />";}
			}
			if($nb_ajout>0) {$msg.="Ajout de $nb_ajout groupe(s) effectué.<br />";}

			if($groupe_hors_enseignement=='y') {
				$sql="SELECT 1=1 FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND matiere='$matiere' AND type='hors_enseignement';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)==0) {
					$sql="INSERT INTO ex_groupes SET id_exam='$id_exam', id_groupe='0', matiere='$matiere', type='hors_enseignement';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {$msg.="Création d'un groupe hors enseignements.<br />";}
				}
			}
		}
		$mode='modif_exam';
	}
/*
	elseif((isset($id_exam))&&($mode=='ajout_matieres')) {
		// Ajout de matières pour l'examen sélectionné
		$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : array());

		$nb_matieres_supprimees=0;
		$tab_matieres_assoc_old=array();
		$sql="SELECT DISTINCT matiere FROM ex_matieres WHERE id_exam='$id_exam';";
		$res=mysql_query($sql);
		while($lig=mysql_fetch_object($res)) {
			$tab_matieres_assoc_old[]=$lig->matiere;

			if(!in_array($lig->matiere,$matiere)) {
				$sql="DELETE FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$lig->matiere';";
				$suppr=mysql_query($sql);

				$sql="DELETE FROM ex_matieres WHERE id_exam='$id_exam' AND matiere='$lig->matiere';";
				$suppr=mysql_query($sql);
				$nb_matieres_supprimees++;
			}
		}

		$nb_matieres_ajoutees=0;
		for($i=0;$i<count($matiere);$i++) {
			if(!in_array($matiere[$i],$tab_matieres_assoc_old)) {
				$sql="INSERT INTO ex_matieres SET id_exam='$id_exam', matiere='$matiere[$i]';";
				$insert=mysql_query($sql);
				if($insert) {$nb_matieres_ajoutees++;}
			}
		}

		if($nb_matieres_supprimees>0) {$msg.="$nb_matieres_supprimees matière(s) supprimée(s) de l'examen $id_exam<br />";}
		if($nb_matieres_ajoutees>0) {$msg.="$nb_matieres_ajoutees matière(s) ajoutée(s) à l'examen $id_exam<br />";}

		$mode="modif_exam";

	}
*/
	elseif((isset($id_exam))&&($mode=='modif_choix_dev')) {
		check_token();

		// Ajout de groupes pour l'examen sélectionnée
		$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : array());
		$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : array());

		// A FAIRE: Contrôler les caractères de $matiere

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'examen n°$id_exam n'existe pas.<br />";
		}
		else {
			$nb_enr=0;
			for($i=0;$i<count($id_groupe);$i++) {
				$id_dev=isset($_POST['id_dev_'.$i]) ? $_POST['id_dev_'.$i] : 0;


				if($id_dev=='Plusieurs_periodes') {
					unset($id_dev_liste_periode);
					$id_dev_liste_periode=isset($_POST['id_dev_'.$i.'_periodes']) ? $_POST['id_dev_'.$i.'_periodes'] : array();
					if(count($id_dev_liste_periode)==0) {
						$msg.="ERREUR: Aucune période n'a été choisie pour le groupe ".$id_groupe[$i].".<br />";
					}
					else {
						$chaine_periodes=$id_dev_liste_periode[0];
						for($j=1;$j<count($id_dev_liste_periode);$j++) {
							$chaine_periodes.=" ".$id_dev_liste_periode[$j];
						}

						$sql="UPDATE ex_groupes SET id_dev='0', type='moy_plusieurs_periodes', valeur='$chaine_periodes' WHERE id_exam='$id_exam' AND id_groupe='$id_groupe[$i]' AND matiere='$matiere';";
						//echo "$sql<br />";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);

						// Et inscrire les valeurs dans ex_notes
						$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='$id_groupe[$i]';";
						//echo "$sql<br />";
						$res_id_ex_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_id_ex_grp)==0) {
							$msg.="Identifiant du groupe dans ex_groupe non trouvé pour l'examen $id_exam et le groupe $id_groupe[$i].<br />";
						}
						else {
							$lig=mysqli_fetch_object($res_id_ex_grp);
							$id_ex_grp=$lig->id;

							// Nettoyage
							$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
							//echo "$sql<br />";
							$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

							unset($tab_note_per);
							for($j=0;$j<count($id_dev_liste_periode);$j++) {
								$sql="SELECT * FROM matieres_notes WHERE id_groupe='$id_groupe[$i]' AND periode='$id_dev_liste_periode[$j]' ORDER BY login;";
								//echo "$sql<br />";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig=mysqli_fetch_object($res)) {
									if($lig->statut=='') {
										$tab_note_per[$lig->login][$lig->periode]=$lig->note;
										//$tab_note_per[$lig->login]['total']
									}
								}
							}
							if(isset($tab_note_per)) {
								foreach($tab_note_per as $ele_login => $tab_notes_eleve) {
									//echo "<p>$ele_login ";
									$total=0;
									foreach($tab_notes_eleve as $tmp_periode => $tmp_note) {
										$total+=$tmp_note;
										//echo $tmp_note." - ";
									}
									//echo "Moyenne: $total/".count($tab_notes_eleve)."<br />";
									$moyenne=round($total*10/count($tab_notes_eleve))/10;
									//$moyenne=str_replace(",", ".", $moyenne);
									$sql="INSERT INTO ex_notes SET id_ex_grp='$id_ex_grp', login='$ele_login', note='$moyenne';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								}
							}
						}
					}
				}
				elseif(mb_substr($id_dev,0,1)=='P') {
					$tmp_per=mb_substr($id_dev,1);
					$sql="UPDATE ex_groupes SET id_dev='0', type='moy_bull', valeur='$tmp_per' WHERE id_exam='$id_exam' AND id_groupe='$id_groupe[$i]' AND matiere='$matiere';";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
				}
				elseif(mb_substr($id_dev,0,4)=='epb_') {
					$tmp_id_epreuve=mb_substr($id_dev,4);
					// On vérifie que l'épreuve existe:
					$sql="SELECT 1=1 FROM eb_epreuves ee, eb_groupes eg WHERE ee.id=eg.id_epreuve AND eg.id_groupe='$id_groupe[$i]';";
					//echo "$sql<br />";
					$test_epreuve=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_epreuve)>0) {
						$sql="UPDATE ex_groupes SET id_dev='0', type='epreuve_blanche', valeur='$tmp_id_epreuve' WHERE id_exam='$id_exam' AND id_groupe='$id_groupe[$i]' AND matiere='$matiere';";
						//echo "$sql<br />";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
					}
					else {
						$msg.="L'épreuve n°$tmp_id_epreuve n'est pas associée au groupe $id_groupe[$i].<br />";
					}
				}
				else {
					// Vérifier que c'est un devoir valide.
					$sql="SELECT 1=1 FROM cn_devoirs WHERE id='$id_dev';";
					//echo "$sql<br />";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)==0) {
						$msg.="Devoir $id_dev invalide pour le groupe $id_groupe[$i].<br />";
					}
					else {
						$sql="UPDATE ex_groupes SET id_dev='$id_dev', type='', valeur='' WHERE id_exam='$id_exam' AND id_groupe='$id_groupe[$i]' AND matiere='$matiere';";
						//echo "$sql<br />";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
					}
				}

/*
				if($id_dev==-1) {
					$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='$id_groupe[$i]' AND matiere='$matiere';";
					$res=mysql_query($sql);
					$lig=mysql_fetch_object($res);
					$sql="SELECT DISTINCT login FROM ex_notes WHERE id_ex_grp='$lig->id';";
					$res2=mysql_query($sql);
					$tab_ele2=array();
					while($lig2=mysql_fetch_object($res2)) {
						$tab_ele2[]=$lig2->login;
					}

					$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$id_groupe[$i]';";
					$res1=mysql_query($sql);
					while($lig1=mysql_fetch_object($res1)) {
						//$tab_ele1[]=$lig1->login;
						if(!in_array($lig1->login,$tab_ele2)) {
							$sql="INSERT INTO ex_notes SET id_ex_grp='$lig->id', login='$lig1->login', statut='v';";
							$insert=mysql_query($sql);
						}
					}
				}
*/
				$nb_enr++;
			}

			if($nb_enr>0) {$msg.="Mise à jour de la liste des devoirs effectuée.<br />";}
		}
		$mode='modif_exam';
	}
	elseif((isset($id_exam))&&($mode=='modif_exam')&&(isset($_GET['select_grp']))&&($_GET['select_grp']=='all')) {
		check_token();

		// Ajout de groupes pour l'examen sélectionnée
		$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : array());

		// A FAIRE: Contrôler les caractères de $matiere

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'examen n°$id_exam n'existe pas.<br />";
		}
		else {

			$groupes_non_visibles['cn']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['cn'][]=$lig_vis->id_groupe;
			}
			$groupes_non_visibles['bull']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['bull'][]=$lig_vis->id_groupe;
			}

			$id_classe=array();
			$sql="SELECT DISTINCT id_classe FROM ex_classes WHERE id_exam='$id_exam';";
			$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_clas=mysqli_fetch_object($res_clas)) {
				$id_classe[]=$lig_clas->id_classe;
			}

			if(!is_array($matiere)) {
				$tmp_matiere=$matiere;
				$matiere=array($tmp_matiere);
			}

			if(count($matiere)==0) {
				$sql="SELECT DISTINCT matiere FROM ex_matieres WHERE id_exam='$id_exam';";
				//echo "$sql<br />";
				$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_mat=mysqli_fetch_object($res_mat)) {
					$matiere[]=$lig_mat->matiere;
				}
			}

			$nb_enr=0;
			for($j=0;$j<count($matiere);$j++) {
				for($i=0;$i<count($id_classe);$i++) {
					$sql="SELECT g.* FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe=g.id AND jgc.id_classe='$id_classe[$i]' AND jgm.id_matiere='$matiere[$j]' AND jgm.id_groupe=jgc.id_groupe AND g.id NOT IN (SELECT DISTINCT id_groupe FROM ex_groupes WHERE id_exam='$id_exam') ORDER BY g.name;";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							if((!in_array($lig->id, $groupes_non_visibles['cn']))||(!in_array($lig->id, $groupes_non_visibles['bull']))) {
								$sql="INSERT INTO ex_groupes SET id_exam='$id_exam', matiere='$matiere[$j]', id_groupe='$lig->id';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {$nb_enr++;}
							}
						}
					}
				}
			}
			if($nb_enr>0) {$msg.="Mise à jour de la liste des groupes effectuée ($nb_enr groupe(s) ajouté(s)).<br />";}
		}
		$mode='modif_exam';
	}
	elseif((isset($id_exam))&&($mode=='modif_exam')&&(isset($_GET['select_moy']))&&((is_numeric($_GET['select_moy']))||($_GET['select_moy']=='moy_plusieurs_periodes'))) {
		check_token();

		// Ajout de groupes pour l'examen sélectionnée
		$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : array());

		// A FAIRE: Contrôler les caractères de $matiere

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'examen n°$id_exam n'existe pas.<br />";
		}
		else {
			/*
			$groupes_non_visibles['cn']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n';";
			$res_vis=mysql_query($sql);
			while($lig_vis=mysql_fetch_object($res_vis)) {
				$groupes_non_visibles['cn'][]=$lig_vis->id_groupe;
			}
			*/
			$groupes_non_visibles['bull']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['bull'][]=$lig_vis->id_groupe;
			}

			/*
			$id_classe=array();
			$sql="SELECT DISTINCT id_classe FROM ex_classes WHERE id_exam='$id_exam';";
			$res_clas=mysql_query($sql);
			while($lig_clas=mysql_fetch_object($res_clas)) {
				$id_classe[]=$lig_clas->id_classe;
			}
			*/
			if(!is_array($matiere)) {
				$tmp_matiere=$matiere;
				$matiere=array($tmp_matiere);
			}

			if(count($matiere)==0) {
				$sql="SELECT DISTINCT matiere FROM ex_matieres WHERE id_exam='$id_exam';";
				//echo "$sql<br />";
				$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_mat=mysqli_fetch_object($res_mat)) {
					$matiere[]=$lig_mat->matiere;
				}
			}

			$nb_enr=0;
			for($j=0;$j<count($matiere);$j++) {
				$sql="SELECT eg.* FROM ex_groupes eg, j_groupes_matieres jgm WHERE jgm.id_groupe=eg.id_groupe AND jgm.id_matiere='$matiere[$j]' AND id_exam='$id_exam';";
				//echo "<br /><p>$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						//echo "Groupe courant: $lig->id_groupe<br />";
						if(!in_array($lig->id, $groupes_non_visibles['bull'])) {
							if(is_numeric($_GET['select_moy'])) {
								$sql="UPDATE ex_groupes SET type='moy_bull', id_dev='0', valeur='".$_GET['select_moy']."' WHERE id_exam='$id_exam' AND matiere='$matiere[$j]' AND id_groupe='$lig->id_groupe';";
								//echo "$sql<br />\n";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {$nb_enr++;}
							}
							else {
								$liste_per_moy="";
								$sql="SELECT DISTINCT periode FROM matieres_notes WHERE id_groupe='".$lig->id_groupe."' ORDER BY periode;";
								//echo "$sql<br />\n";
								$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
								$cpt_per=0;
								$id_dev_liste_periode=array();
								while($lig_per=mysqli_fetch_object($res_per)) {
									if($cpt_per>0) {$liste_per_moy.=" ";}
									$liste_per_moy.=$lig_per->periode;

									$id_dev_liste_periode[]=$lig_per->periode;

									$cpt_per++;
								}

								// Et inscrire les valeurs dans ex_notes
								$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='".$lig->id_groupe."';";
								//echo "$sql<br />";
								$res_id_ex_grp=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_id_ex_grp)==0) {
									$msg.="Identifiant du groupe dans ex_groupe non trouvé pour l'examen $id_exam et le groupe ".$lig->id_groupe.".<br />";
								}
								else {
									$lig_eg=mysqli_fetch_object($res_id_ex_grp);
									$id_ex_grp=$lig_eg->id;

									// Nettoyage
									$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
									$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

									unset($tab_note_per);
									for($jj=0;$jj<count($id_dev_liste_periode);$jj++) {
										$sql="SELECT * FROM matieres_notes WHERE id_groupe='".$lig->id_groupe."' AND periode='$id_dev_liste_periode[$jj]' ORDER BY login;";
										//echo "$sql<br />";
										$res_mn=mysqli_query($GLOBALS["mysqli"], $sql);
										while($lig_mn=mysqli_fetch_object($res_mn)) {
											if($lig_mn->statut=='') {
												$tab_note_per[$lig_mn->login][$lig_mn->periode]=$lig_mn->note;
												//$tab_note_per[$lig->login]['total']
											}
										}
									}
									if(isset($tab_note_per)) {
										foreach($tab_note_per as $ele_login => $tab_notes_eleve) {
											//echo "<p>$ele_login ";
											$total=0;
											foreach($tab_notes_eleve as $tmp_periode => $tmp_note) {
												$total+=$tmp_note;
												//echo $tmp_note." - ";
											}
											//echo "Moyenne: $total/".count($tab_notes_eleve)."<br />";
											$moyenne=round($total*10/count($tab_notes_eleve))/10;
											//$moyenne=str_replace(",", ".", $moyenne);
											$sql="INSERT INTO ex_notes SET id_ex_grp='$id_ex_grp', login='$ele_login', note='$moyenne';";
											//echo "$sql<br />";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										}
									}
								}

								$sql="UPDATE ex_groupes SET type='moy_plusieurs_periodes', id_dev='0', valeur='".$liste_per_moy."' WHERE id_exam='$id_exam' AND matiere='$matiere[$j]' AND id_groupe='$lig->id_groupe';";
								//echo "$sql<br />\n";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {$nb_enr++;}
							}
						}
					}
				}
			}
			if($nb_enr>0) {$msg.="Mise à jour de la liste des évaluations effectuée.<br />";}
		}
		$mode='modif_exam';
	}


/*
	elseif((isset($id_exam))&&($mode=='suppr_groupe')) {
		// Ajout de groupes pour l'épreuve sélectionnée
		$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;

		if(isset($id_groupe)) {
			$sql="SELECT 1=1 FROM eb_copies ec, eb_groupes eg WHERE ec.id_exam='$id_exam' AND eg.id_exam='$id_exam' AND eg.id_groupe='$id_groupe' AND statut!='v';";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==1) {
				$msg="Une note a déjà été saisie pour une copie associée au groupe.";
			}
			elseif(mysql_num_rows($test)>1) {
				$msg=mysql_num_rows($test)." notes ont déjà été saisies pour des copies associées au groupe.";
			}
			else {
				$sql="DELETE FROM eb_copies ec, eb_groupes eg WHERE ec.id_exam='$id_exam' AND eg.id_exam='$id_exam' AND eg.id_groupe='$id_groupe';";
				$suppr=mysql_query($sql);
				if(!$suppr) {
					$msg="ERREUR lors de la suppression des copies associées au groupe n°$id_groupe.";
				}
				else {
					$sql="DELETE FROM eb_groupes WHERE id_exam='$id_exam' AND id_groupe='$id_groupe';";
					$suppr=mysql_query($sql);
					if(!$suppr) {
						$msg="ERREUR lors de la suppression du groupe n°$id_groupe.";
					}
					else {
						$msg="Suppression du groupe n°$id_groupe effectuée.";
					}
				}
			}
		}
		$mode='modif_exam';
	}
	elseif((isset($id_exam))&&($mode=='ajout_profs')) {
		// Ajout de groupes pour l'épreuve sélectionnée
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : array());

		$sql="DELETE FROM eb_profs WHERE id_exam='$id_exam';";
		$suppr=mysql_query($sql);
		if(!$suppr) {
			$msg="ERREUR lors de la réinitialisation des professeurs inscrits.";
		}
		else {
			$sql="SELECT * FROM eb_epreuves WHERE id='$id_exam';";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$msg="L'épreuve n°$id_exam n'existe pas.<br />";
			}
			else {
				$tab_profs_inscrits=array();
				$msg="";
				for($i=0;$i<count($login_prof);$i++) {
					// On peut sélectionner plusieurs fois le même prof, mais il ne faut pas l'insérer plusieurs fois dans la table eb_profs
					if(!in_array($login_prof[$i],$tab_profs_inscrits)) {
						$tab_profs_inscrits[]=$login_prof[$i];
						$sql="INSERT INTO eb_profs SET id_exam='$id_exam', login_prof='$login_prof[$i]';";
						$insert=mysql_query($sql);
						if(!$insert) {
							$msg.="Erreur lors de l'ajout du professeur $login_prof[$i]<br />";
						}
					}
				}
				if(($msg=='')&&(count($login_prof)>0)) {$msg="Ajout de(s) professeur(s) effectué.";}

				// Vérification:
				// A-t-on supprimé un prof qui était associé à des copies?
				$sql="SELECT DISTINCT login_prof FROM eb_copies WHERE id_exam='$id_exam' AND login_prof!='';";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					//$tab_profs_associes_copies=array();
					while($lig=mysql_fetch_object($res)) {
						//$tab_profs_associes_copies
						if(!in_array($lig->login_prof,$tab_profs_inscrits)) {
							$sql="UPDATE eb_copies SET login_prof='' WHERE id_exam='$id_exam' AND login_prof='$lig->login_prof';";
							$update=mysql_query($sql);
							$msg.="Suppression de professeur(s) qui étai(en)t associé(s) à des copies.<br />";
						}
					}
				}
			}
		}
		$mode='modif_exam';
	}
	elseif((isset($id_exam))&&($mode=='clore')) {
		// Cloture d'une épreuve
		$sql="UPDATE eb_epreuves SET etat='clos' WHERE id='$id_exam';";
		$cloture=mysql_query($sql);
		if(!$cloture) {
			$msg="ERREUR lors de la cloture de l'épreuve $id_exam";
			unset($id_exam);
			unset($mode);
			break;
		}
		else {$msg="Cloture de l'épreuve n°$id_exam effectuée.";}
		unset($id_exam);
		unset($mode);
	}
	elseif((isset($id_exam))&&($mode=='declore')) {
		// Réouverture d'une épreuve
		$sql="UPDATE eb_epreuves SET etat='' WHERE id='$id_exam';";
		$cloture=mysql_query($sql);
		if(!$cloture) {
			$msg="ERREUR lors de la réouverture de l'épreuve $id_exam";
			unset($id_exam);
			unset($mode);
			break;
		}
		else {
			$msg="Réouverture de l'épreuve n°$id_exam effectuée.";
			$mode='modif_exam';
		}
	}

	if($temoin_erreur_n_anonymat=='y') {
		if(!isset($msg)) {$msg="";}
		$msg.="<br />Une ou des erreurs se sont produites sur l'anonymat.<br />Vous devriez contrôler les numéros anonymat.";
	}
	elseif($temoin_n_anonymat=='y') {
		if(!isset($msg)) {$msg="";}
		$msg.="<br />Des numéros anonymat ont été modifiés. Regénérez si nécessaire les étiquettes/listes d'émargement.";
	}
	*/
}


/*
$truncate_tables=isset($_GET['truncate_tables']) ? $_GET['truncate_tables'] : NULL;
if($truncate_tables=='y') {
	$msg="<p>Nettoyage des tables Génèse des classes... <font color='red'>A FAIRE</font></p>\n";
	$sql="TRUNCATE TABLE ...;";
	//$del=mysql_query($sql);
}
*/

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$javascript_specifique[]='mod_examen_blanc/lib_exb';

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Examen blanc: Accueil";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='../accueil.php'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Accueil</a>";
//echo "</p>\n";
//echo "</div>\n";

//include("../lib/calendrier/calendrier.class.php");

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=="y")) {
	if(!isset($id_exam)) {

		//echo "<h2>Epreuve blanche</h2>\n";
		//echo "<blockquote>\n";

		if(!isset($mode)) {
			echo "</p>\n";

			echo "<ul>\n";
			// Créer un examen blanc
			echo "<li>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=creer_exam'>Créer un nouvel examen</a></p>\n";
			echo "</li>\n";

			// Accéder aux examens blancs
			$sql="SELECT * FROM ex_examens ORDER BY date, intitule;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				echo "<li>\n";
				echo "<p><b>Examens blancs&nbsp;:</b><br />\n";
				while($lig=mysqli_fetch_object($res)) {
					$afficher_cet_examen_blanc="y";
					if(($_SESSION['statut']=='professeur')&&(!is_pp_proprio_exb($lig->id))) {$afficher_cet_examen_blanc="n";}

					if($afficher_cet_examen_blanc=="y") {
						echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_exam=$lig->id&amp;mode=modif_exam'";
						if($lig->description!='') {
							echo " onmouseover=\"delais_afficher_div('div_exam_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_exam_".$lig->id."')\"";
	
							$titre="Examen n°$lig->id";
							$texte="<p><b>".$lig->intitule."</b><br />";
							$texte.=$lig->description;
							$tabdiv_infobulle[]=creer_div_infobulle('div_exam_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');
	
						}
						echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)";
						echo " - <a href='".$_SERVER['PHP_SELF']."?id_exam=$lig->id&amp;mode=suppr_exam".add_token_in_url()."' onclick=\"return confirm('Etes vous sûr de vouloir supprimer l examen?')\">Supprimer</a>";
						//echo " (<em>créé par...</em>)";
						echo "<br />\n";
					}
				}
				echo "</li>\n";
			}
			echo "</ul>\n";

			echo "<p style='color:red'>A FAIRE ENCORE&nbsp;: Un lien pour vider toutes les tables d'examens blancs.<br />Est-ce qu'il faut vider ces tables lors de l'initialisation?<br />Si oui, peut-être ajouter une conservation dans les tables archivages (années antérieures).</p>\n";
		}
		//===========================================================================
		// Création d'un examen
		elseif($mode=='creer_exam') {
			echo " | <a href='".$_SERVER['PHP_SELF']."'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Menu examens blancs</a>\n";
			echo "</p>\n";

			echo "<p class='bold'>Création d'un examen blanc&nbsp;:</p>\n";

			echo "<blockquote>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			echo "<table summary='Paramètres'>\n";
			echo "<tr>\n";
			echo "<td>Intitule&nbsp;:</td>\n";
			echo "<td><input type='text' name='intitule' value='Examen blanc' onchange='changement()' /></td>\n";
			echo "</tr>\n";

			//$cal = new Calendrier("form1", "date");
		
			$annee=strftime("%Y");
			$mois=strftime("%m");
			$jour=strftime("%d");
			$date_defaut=$jour."/".$mois."/".$annee;

			echo "<tr>\n";
			echo "<td>Date de l'examen&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='date' value='$date_defaut' />\n";
			//echo "<input type='text' name='date' id='date_examen' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
			//echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";

			echo "<input type='text' name='date' id='date_examen' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date(this.id,event);\" />\n";
			echo img_calendrier_js("date_examen", "img_bouton_date_examen");
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td>Description&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='description' value='' />";
			echo "<textarea class='wrap' name=\"no_anti_inject_description\" rows='4' cols='40' onchange='changement()'></textarea>\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2' align='center'><input type='submit' name='creer_exam' value='Valider' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			//echo "<input type='hidden' name='is_posted' value='2' />\n";
			echo "</form>\n";
			echo "</blockquote>\n";

			//echo "<p style='color:red'>NOTES&nbsp;:</p>";
			//echo "<ul>";
			//echo "<li><p style='color:red'>...</p></li>\n";
			//echo "</ul>";
		}
	}
	//===========================================================================
	// Modification/compléments sur une examen
	elseif($mode=='modif_exam') {
		echo " | <a href='".$_SERVER['PHP_SELF']."'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Menu examens blancs</a>\n";

		$aff=isset($_POST['aff']) ? $_POST['aff'] : (isset($_GET['aff']) ? $_GET['aff'] : NULL);
		if(!isset($aff)) {
			echo "</p>\n";

			echo "<p><b>Modification d'un examen blanc&nbsp;:</b> Examen n°$id_exam</p>\n";

			$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'examen $id_exam n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			//$etat=$lig->etat;

			//==============================================
			// Requêtes exploitées plus bas
			//$sql="SELECT g.*,m.matiere,m.nom_complet FROM ex_matieres em, ex_groupes eg, groupes g WHERE eg.id_exam='$id_exam' AND em.id_exam='$id_exam' AND em.matiere=eg.matiere AND em.matiere=m.matiere ORDER BY em.ordre,m.matiere;";
			//$res_groupes=mysql_query($sql);

			//if(mysql_num_rows($res_groupes)>0) {

				echo "<div class='fieldset_opacite50' style='float:right; width:15em; border: 1px solid black;'>\n";

				echo "<ul>\n";

				$sql="SELECT DISTINCT id FROM ex_examens WHERE id!='$id_exam';";
				$res_autres_exam=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_autres_exam)>0) {
					$acces_copie_exam="y";
					if($_SESSION['statut']=='professeur') {
						$acces_copie_exam="n";
						while($lig_autres_exam=mysqli_fetch_object($res_autres_exam)) {
							if(is_pp_proprio_exb($lig_autres_exam->id)) {
								$acces_copie_exam="y";
								break;
							}
						}
					}

					if($acces_copie_exam=="y") {
						echo "<li>\n";
						echo "<p><a href='copie_exam.php?id_exam=$id_exam'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Copier les paramètres d'un autre examen blanc</a></p>\n";
						echo "</li>\n";
					}
				}

				echo "<li>\n";
				$sql="SELECT c.classe, ec.id_classe FROM ex_classes ec, classes c WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
				//echo "$sql<br />";
				$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_classes=mysqli_num_rows($res_classes);
				if($nb_classes>0) {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=classes'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Modifier la liste des classes</a></p>\n";
				}
				else {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=classes'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Ajouter des classes</a></p>\n";
				}
				echo "</li>\n";
				echo "<li>\n";
				//$sql="SELECT m.*,em.coef,em.bonus FROM ex_matieres em, matieres m WHERE em.matiere=m.matiere AND id_exam='$id_exam' ORDER BY em.ordre, m.matiere;";
				// Pour mettre les matières à bonus à la fin si aucun ordre n'a été défini
				$sql="SELECT m.*,em.coef,em.bonus FROM ex_matieres em, matieres m WHERE em.matiere=m.matiere AND id_exam='$id_exam' ORDER BY em.ordre, em.bonus, m.matiere;";
				$res_matieres=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_matieres=mysqli_num_rows($res_matieres);
				if($nb_matieres>0) {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=matieres'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Modifier la liste des matières</a></p>\n";
				}
				else {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=matieres'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Ajouter des matières</a></p>\n";
				}
				echo "</li>\n";
				echo "<li>\n";
				echo "<p><a href='releve.php?id_exam=$id_exam'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo " title=\"L'édition des relevés de notes permet aussi des exports CSV et PDF.\"";
				echo ">Editer des relevés de notes et moyennes</a></p>\n";
				echo "</li>\n";

				echo "<li>\n";
				echo "<p><a href='bull_exb.php?id_exam=$id_exam'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">Editer des bulletins</a></p>\n";
				echo "</li>\n";
				echo "</ul>\n";

				echo "</div>\n";
			//}
			//==============================================

			echo "<blockquote>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			//echo "<fieldset style='padding: 8px;  margin: 8px;'>\n";
			//echo "<div style='border: 1px solid black;'>\n";

			echo "<table style='border: 1px solid black;' summary='Paramètres'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Intitule&nbsp;:</td>\n";
			//if($etat!='clos') {
				echo "<td><input type='text' name='intitule' value='$lig->intitule' size='34' onchange='changement()' /></td>\n";
			//}
			//else {
			//	echo "<td>$lig->intitule</td>\n";
			//}
			echo "</tr>\n";
	
			//$cal = new Calendrier("form1", "date");
	
			/*
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
			$date_defaut=$jour."/".$mois."/".$annee;
			*/
			$tab=explode("-",$lig->date);
			$annee=$tab[0];
			$mois=$tab[1];
			$jour=$tab[2];
			$date_defaut=$jour."/".$mois."/".$annee;
	
			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Date de l'examen&nbsp;:</td>\n";
			echo "<td>\n";

			//if($etat!='clos') {
				//echo "<input type='text' name='date' value='$date_defaut' size='10' onchange='changement()' />\n";
				//echo "<input type='text' name='date' id='date_examen' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
				//echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";

				echo "<input type='text' name='date' id='date_examen' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date(this.id,event);\" />\n";
				echo img_calendrier_js("date_examen", "img_bouton_date_examen");

			//}
			//else {
			//	echo $date_defaut;
			//}

			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td style='font-weight:bold; vertical-align:top;'>Description&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='description' value='' />";
			//if($etat!='clos') {
				echo "<textarea class='wrap' name=\"no_anti_inject_description\" rows='4' cols='40' onchange='changement()'>".$lig->description."</textarea>\n";
			//}
			//else {
			//	echo nl2br($lig->description);
			//}
			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td colspan='2' align='center'><input type='submit' name='modif_exam' value='Valider' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
			echo "<input type='hidden' name='mode' value='modif_exam' />\n";
			//echo "</fieldset>\n";
			//echo "</div>\n";
			echo "</form>\n";
			echo "</blockquote>\n";

			//echo "<div style='clear:both;'></div>";

			//=================================
			echo "<table border='0' summary='Mise en forme'>\n";
			echo "<tr>\n";
			echo "<td valign='top'>\n";
			//$sql="SELECT c.classe, ec.id_classe FROM ex_classes ec, classes c WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			//echo "$sql<br />";
			//$res_classes=mysql_query($sql);
			$tab_id_classe=array();
			$tab_classe=array();
			//$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes>0) {
				echo "<p class='bold'>Liste des classes&nbsp;:</p>\n";
				echo "<ul>\n";
				while($lig=mysqli_fetch_object($res_classes)) {
					$tab_id_classe[]=$lig->id_classe;
					$tab_classe[]=$lig->classe;
					echo "<li>$lig->classe</li>\n";
				}
				echo "</ul>\n";
				//echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=classes'>Modifier la liste des classes</a></p>\n";
			}
			/*
			else {
				echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=classes'>Ajouter des classes</a></p>\n";
			}
			*/
			echo "</td>\n";
			//=================================
			echo "<td valign='top'>\n";
			//$sql="SELECT m.*,em.coef,em.bonus FROM ex_matieres em, matieres m WHERE em.matiere=m.matiere ORDER BY em.ordre, m.matiere;";
			//$res_matieres=mysql_query($sql);
			$tab_matiere=array();
			$tab_coef=array();
			$tab_bonus=array();
			//$nb_matieres=mysql_num_rows($res_matieres);
			if($nb_matieres>0) {
				echo "<p class='bold'>Liste des matières&nbsp;:</p>\n";
				echo "<ul>\n";
				while($lig=mysqli_fetch_object($res_matieres)) {
					$tab_matiere[]=$lig->matiere;
					$tab_coef[]=$lig->coef;
					$tab_bonus[]=$lig->bonus;
					echo "<li>".htmlspecialchars($lig->matiere)." (<i>".htmlspecialchars($lig->nom_complet)."</i>)</li>\n";
				}
				echo "</ul>\n";
				//echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=matieres'>Modifier la liste des matières</a></p>\n";
			}
			/*
			else {
				echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;aff=matieres'>Ajouter des matières</a></p>\n";
			}
			*/
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			//=================================

			if((count($tab_matiere)>0)&&(count($tab_classe)>0)) {
				$tab_periodes_avec_moy=array();
				$tab_periodes_classes=array();
				for($i=0;$i<count($tab_id_classe);$i++) {
					// Faut-il restreindre aux périodes avec moyennes sur les bulletins?
					// On peut souhaiter préparer un examen blanc en début d'année, avant le remplissage des bulletins
					$sql="SELECT DISTINCT mn.periode FROM matieres_notes mn, j_eleves_classes jec WHERE jec.id_classe='".$tab_id_classe[$i]."' AND mn.login=jec.login AND jec.periode=mn.periode ORDER BY periode;";
					//echo "$sql<br />";
					$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_per=mysqli_fetch_object($res_per)) {
						if(!in_array($lig_per->periode, $tab_periodes_avec_moy)) {
							$tab_periodes_avec_moy[]=$lig_per->periode;
						}
					}

					$sql="SELECT DISTINCT num_periode FROM periodes WHERE id_classe='".$tab_id_classe[$i]."' ORDER BY num_periode;";
					//echo "$sql<br />";
					$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_per=mysqli_fetch_object($res_per)) {
						if(!in_array($lig_per->num_periode, $tab_periodes_classes)) {
							$tab_periodes_classes[]=$lig_per->num_periode;
						}
					}
				}
				sort($tab_periodes_avec_moy);
				sort($tab_periodes_classes);

				echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form2'>\n";
				echo add_token_field();

				echo "<a name='choix_groupes_dev'></a>";
				echo "<p class='bold'>Choix des groupes et devoirs&nbsp;:</p>\n";

				$grp_hors_enseignement='n';
				$sql="SELECT 1=1 FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND type='hors_enseignement';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {$grp_hors_enseignement='y';}

				echo "<table class='boireaus' border='1' summary='Tableau des associations matières/classes/groupes'>\n";
				echo "<tr>\n";
				echo "<th>Classes<br />Matières</th>\n";
				echo "<th>Groupes";
				echo "<a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;select_grp=all".add_token_in_url()."#choix_groupes_dev'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo "><img src='../images/icons/wizard.png' width='16' height='16' title=\"Sélectionner tous les groupes présents sur les Bulletins ou Carnets de notes (parmi les matières choisies pour cet examen blanc)\" /></a>";
				echo "<br />Devoirs";

				echo "<br />";
				//for($loop=0;$loop<count($tab_periodes_avec_moy);$loop++) {
				for($loop=0;$loop<count($tab_periodes_classes);$loop++) {
					if($loop>0) {echo " -";}
					echo " <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;select_moy=".$tab_periodes_classes[$loop].add_token_in_url()."#choix_groupes_dev'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo " title=\"Sélectionner les moyennes des bulletins en période n°".$tab_periodes_classes[$loop]." pour faire office d'évaluations\">";
					if(!in_array($tab_periodes_classes[$loop], $tab_periodes_avec_moy)) {
						echo "<span style='color:red' title='Pas de moyennes à ce jour dans cette période'>";
						echo "P".$tab_periodes_classes[$loop];
						echo "</span>";
					}
					else {
						echo "P".$tab_periodes_classes[$loop];
					}
					echo "</a>";
				}
				echo " - <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;mode=modif_exam&amp;select_moy=moy_plusieurs_periodes".add_token_in_url()."#choix_groupes_dev'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo " title=\"Sélectionner les moyennes de toutes les périodes (avec notes) des bulletins pour faire office d'évaluations\">";
				echo "Toutes";
				echo "</a>";

				echo"</th>\n";
				echo "<th>Coef</th>\n";
				echo "<th>Bonus</th>\n";
				for($i=0;$i<count($tab_classe);$i++) {
					echo "<th>$tab_classe[$i]</th>\n";
				}
				if($grp_hors_enseignement=='y') {echo "<th>Hors enseignements</th>\n";}
				echo "</tr>\n";

				$tab_dev=array();
				$tab_bull=array();
				$tab_moy_plusieurs_periodes=array();
				$tab_epb=array();
				$alt=1;
				for($j=0;$j<count($tab_matiere);$j++) {
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<th>".htmlspecialchars($tab_matiere[$j])."\n";
					echo "<input type='hidden' name='tab_matiere[$j]' value='$tab_matiere[$j]' size='2' />\n";
					echo "</th>\n";

					echo "<th>\n";

					//echo "<a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;id_classe=$tab_id_classe[$i]&amp;mode=modif_exam&amp;aff=groupes'>Choix groupes</a><br />\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;mode=modif_exam&amp;aff=groupes'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Choix des groupes</a>";

					echo "<a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;mode=modif_exam&amp;select_grp=all".add_token_in_url()."#choix_groupes_dev'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo "'><img src='../images/icons/wizard.png' width='16' height='16' title=\"Sélectionner tous les groupes de $tab_matiere[$j] présents sur les Bulletins ou Carnets de notes\" /></a>";

					echo "<br />\n";

					//$sql="SELECT 1=1 FROM ex_groupes eg WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' LIMIT 1;";
					//$sql="SELECT 1=1 FROM ex_groupes eg WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND type!='hors_enseignement' LIMIT 1;";
					$sql="SELECT 1=1 FROM ex_groupes eg, groupes g WHERE g.id=eg.id_groupe AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND type!='hors_enseignement' LIMIT 1;";
					//echo "$sql<br />";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						echo " <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;mode=modif_exam&amp;aff=choix_dev'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Choix des devoirs</a>";
					}
					echo "</th>\n";

					// Mettre un javascript pour augmenter/réduire le coef avec les flèches: FAIT
					echo "<td><input type='text' name='coef[$j]' id='coef_$j' value='$tab_coef[$j]' size='2' onKeyDown=\"nombre_plus_moins(this.id,event);\"  onchange='changement()' /></td>\n";
					echo "<td><input type='checkbox' name='bonus[$j]' id='bonus_$j' value='y' onchange='changement()' ";
					if($tab_bonus[$j]=='y') {echo "checked ";}
					echo "/></td>\n";

					for($i=0;$i<count($tab_classe);$i++) {
						echo "<td>\n";

						//$sql="SELECT DISTINCT g.*, eg.type, eg.id_dev FROM groupes g, ex_groupes eg, j_groupes_classes jgc WHERE g.id=eg.id_groupe AND g.id=jgc.id_groupe AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' ORDER BY g.name, g.description;";
						$sql="SELECT DISTINCT g.*, eg.type, eg.id_dev, eg.valeur FROM groupes g, ex_groupes eg, j_groupes_classes jgc WHERE g.id=eg.id_groupe AND g.id=jgc.id_groupe AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_classe='$tab_id_classe[$i]' ORDER BY g.name, g.description;";
						//echo "$sql<br />";
						$res_groupes=mysqli_query($GLOBALS["mysqli"], $sql);

						if(mysqli_num_rows($res_groupes)>0) {
							//echo "<ul>\n";
							$cpt_grp=0;
							while($lig=mysqli_fetch_object($res_groupes)) {
								if($cpt_grp>0) {echo "<br />";}
								// Groupe
								echo $lig->name;
								// Suppr groupe
								//echo " <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;mode=suppr_grp'>Suppr</a>";
								// Choix du devoir
								//echo " <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;mode=modif_exam&amp;aff=choix_dev'>Devoir</a>";
								if($lig->id_dev>0) {
									echo "<br />\n";
									echo "<span style='font-size:small;'>\n";
									//echo "Devoir n°$lig->id_dev\n";

									$sql="SELECT cd.nom_court, cd.nom_complet, cd.description, cd.date, ccn.periode FROM cn_devoirs cd, cn_cahier_notes ccn WHERE ccn.id_cahier_notes=cd.id_racine AND cd.id='$lig->id_dev';";
									$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_dev)==0) {
										echo "Devoir n°$lig->id_dev\n";
										echo "<span style='color:red;'>ERREUR&nbsp;: Devoir inconnu???</span>";
									}
									else {
										$lig_dev=mysqli_fetch_object($res_dev);

										echo "<a href='#' onmouseover=\"delais_afficher_div('div_dev_".$lig->id_dev."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_dev_".$lig->id_dev."')\" onclick='return false;'>";
										echo "Devoir n°$lig->id_dev";
										echo "</a>\n";

										if(!in_array($lig->id_dev,$tab_dev)) {
											$tab_dev[]=$lig->id_dev;

											$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig_dev->periode' AND id_classe='$tab_id_classe[$i]';";
											$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
											$lig_per=mysqli_fetch_object($res_per);
	
											$titre="Devoir n°$lig->id_dev (<i>$lig_per->nom_periode</i>)";
											$texte="<p><b>".htmlspecialchars($lig_dev->nom_court)."</b>";
											if($lig_dev->nom_court!=$lig_dev->nom_complet) {
												$texte.=" (<i>".htmlspecialchars($lig_dev->nom_complet)."</i>)";
											}
											$texte.="<br />";
											if($lig_dev->description!='') {
												$texte.=htmlspecialchars($lig_dev->description);
											}
	
											// Nombre de notes saisies
											$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$lig->id_dev';";
											$res_cnd=mysqli_query($GLOBALS["mysqli"], $sql);
											$eff_tot=mysqli_num_rows($res_cnd);
											if($eff_tot>0) {
												$texte.="<br />\n";
												$eff_non_vide=0;
												while($lig_cnd=mysqli_fetch_object($res_cnd)) {
													if($lig_cnd->statut!='v') {$eff_non_vide++;}
												}
												if($eff_non_vide==$eff_tot) {$texte.="<span style='color:green;'>$eff_non_vide/$eff_tot</span>";}
												else {$texte.="<span style='color:red;'>$eff_non_vide/$eff_tot</span>";}
											}
											else {
												$texte.="<span style='color:red;'>Aucune note saisie</span>";
											}
	
											$tabdiv_infobulle[]=creer_div_infobulle('div_dev_'.$lig->id_dev,$titre,"",$texte,"",30,0,'y','y','n','n');
										}
									}
									echo "</span>\n";
								}
								elseif($lig->type=='moy_bull') {
									echo "<br />\n";
									echo "<span style='font-size:small;'>\n";

									echo "<a href='#' onmouseover=\"delais_afficher_div('div_moy_bull_".$lig->id."_".$lig->valeur."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_moy_bull_".$lig->id."_".$lig->valeur."')\" onclick='return false;'>";
									echo "Moy.bulletin P".$lig->valeur;
									echo "</a>\n";


									if(!in_array("moy_bull_".$lig->id."_".$lig->valeur,$tab_bull)) {
										$tab_bull[]="moy_bull_".$lig->id."_".$lig->valeur;

										$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig->valeur' AND id_classe='$tab_id_classe[$i]';";
										$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
										$lig_per=mysqli_fetch_object($res_per);

										$titre="Moyennes bulletins (<i>$lig_per->nom_periode</i>)";
										$texte="<p><b>Moyennes des élèves sur les bulletins pour la période $lig_per->nom_periode</b>";

										// Effectif du groupe sur la période
										$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='$lig->id' AND periode='$lig->valeur';";
										//echo "$sql<br />\n";
										$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
										$eff_grp=mysqli_num_rows($res_grp);

										// Nombre de notes saisies
										//$sql="SELECT * FROM matieres_notes WHERE id_groupe='$lig->id' AND periode='$lig->valeur';";
										$sql="SELECT * FROM matieres_notes mn, j_eleves_groupes jeg WHERE jeg.id_groupe=mn.id_groupe AND jeg.periode=mn.periode AND mn.login=jeg.login AND mn.id_groupe='$lig->id' AND mn.periode='$lig->valeur';";
										//echo "$sql<br />\n";
										$res_notes_bull=mysqli_query($GLOBALS["mysqli"], $sql);
										$eff_notes_bull=mysqli_num_rows($res_notes_bull);

										if($eff_notes_bull>0) {
											$texte.="<br />\n";
											$eff_non_note=0;
											$eff_abs=0;
											$eff_disp=0;

											while($lig_nb=mysqli_fetch_object($res_notes_bull)) {
												if($lig_nb->statut=='-') {$eff_non_note++;}
												elseif($lig_nb->statut=='abs') {$eff_abs++;}
												elseif($lig_nb->statut=='disp') {$eff_disp++;}
											}

											if($eff_grp==$eff_notes_bull) {
												$texte.="<span style='color:green;'>$eff_notes_bull/$eff_grp</span>";
											}
											else {
												//$texte.="<span style='color:red;'>$eff_non_vide/$eff_tot</span>";
												$texte.="<span style='color:red;'>$eff_notes_bull/$eff_grp</span>";
											}

											$texte.="<br />\n";
											if($eff_abs>0) {$texte.="$eff_abs absent(s)<br />\n";}
											if($eff_disp>0) {$texte.="$eff_disp dispensé(s)<br />\n";}
											if($eff_non_note>0) {$texte.="$eff_non_note non noté(s)<br />\n";}
										}
										else {
											$texte.="<span style='color:red;'>Aucune moyenne saisie</span>";
										}

										$tabdiv_infobulle[]=creer_div_infobulle("div_moy_bull_".$lig->id."_".$lig->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
									}

									echo "</span>\n";
								}
								elseif($lig->type=='moy_plusieurs_periodes') {

									echo "<br />\n";
									echo "<span style='font-size:small;'>\n";

									echo "<a href='#' onmouseover=\"delais_afficher_div('div_moy_plusieurs_periodes_".$lig->id."_".$lig->valeur."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_moy_plusieurs_periodes_".$lig->id."_".$lig->valeur."')\" onclick='return false;'>";
									echo "Moy.périodes ".$lig->valeur;
									echo "</a>\n";

									$chaine_mpp="moy_plusieurs_periodes_".$lig->id."_".strtr($lig->valeur," ","_");
									if(!in_array($chaine_mpp,$tab_moy_plusieurs_periodes)) {
										$tab_moy_plusieurs_periodes[]=$chaine_mpp;

										$titre="Moyennes des périodes $lig->valeur";
										$texte="<p><b>Moyenne des moyennes des élèves sur les bulletins pour les périodes $lig->valeur</b>";

										// Effectif du groupe sur les périodes
										$tab_per=explode(" ",$lig->valeur);
										$chaine_sql="(periode='$tab_per[0]'";
										for($loop=1;$loop<count($tab_per);$loop++) {
											$chaine_sql.=" OR periode='$tab_per[0]'";
										}
										$chaine_sql.=")";

										$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$lig->id' AND $chaine_sql;";
										//echo "$sql<br />\n";
										$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
										$eff_grp=mysqli_num_rows($res_grp);

										// Nombre de notes saisies
										$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='$lig->id';";
										//echo "$sql<br />";
										$res_id_ex_grp=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_id_ex_grp)==0) {
											$texte.="<span style='color:red'>Identifiant du groupe dans ex_groupe non trouvé pour l'examen $id_exam et le groupe $id_groupe[$i].</span><br />";
										}
										else {
											$lig_ex_grp=mysqli_fetch_object($res_id_ex_grp);
											$id_ex_grp=$lig_ex_grp->id;


											//$sql="SELECT * FROM matieres_notes WHERE id_groupe='$lig->id' AND periode='$lig->valeur';";
											$sql="SELECT * FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
											//echo "$sql<br />\n";
											$res_notes_mpp=mysqli_query($GLOBALS["mysqli"], $sql);
											$eff_notes_mpp=mysqli_num_rows($res_notes_mpp);
	
											if($eff_notes_mpp>0) {
												$texte.="<br />\n";
	
												if($eff_grp==$eff_notes_mpp) {
													$texte.="<span style='color:green;'>$eff_notes_mpp/$eff_grp</span>";
												}
												else {
													$texte.="<span style='color:red;'>$eff_notes_mpp/$eff_grp</span>";
												}
	
												$texte.="<br />\n";
											}
											else {
												$texte.="<span style='color:red;'>Aucune moyenne saisie</span>";
											}
	
											$tabdiv_infobulle[]=creer_div_infobulle("div_moy_plusieurs_periodes_".$lig->id."_".$lig->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
										}
									}

									echo "</span>\n";


								}
								elseif($lig->type=='epreuve_blanche') {
									echo "<br />\n";
									echo "<span style='font-size:small;'>\n";

									echo "<a href='#' onmouseover=\"delais_afficher_div('div_moy_epb_".$lig->id."_".$lig->valeur."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_moy_epb_".$lig->id."_".$lig->valeur."')\" onclick='return false;'>";
									echo "EpB ".$lig->valeur;
									echo "</a>\n";

									if(!in_array("moy_epb_".$lig->id."_".$lig->valeur,$tab_epb)) {
										$tab_epb[]="moy_epb_".$lig->id."_".$lig->valeur;

										$info_epb="";
										$sql="SELECT ee.* FROM eb_epreuves ee, eb_groupes eg WHERE ee.id=eg.id_epreuve AND eg.id_groupe='$lig->id' AND ee.id='$lig->valeur';";
										//echo "$sql<br />\n";
										$res_epreuve=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res)>0) {
											$lig_epb=mysqli_fetch_object($res_epreuve);

											$info_epb="<b>".$lig_epb->intitule."</b> (<em>".formate_date($lig_epb->date)."</em>)<br />".$lig_epb->description."<br />Notée sur ".$lig_epb->note_sur;

											/*
											$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig->valeur' AND id_classe='$tab_id_classe[$i]';";
											$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
											$lig_per=mysqli_fetch_object($res_per);
											*/
										}

										$titre="Épreuve blanche (<i>$lig->valeur</i>)";
										$texte="<p><b>Notes obtenues par les élèves à l'épreuve blanche</b><br />";
										$texte.=$info_epb;

										// Effectif du groupe sur la période... on n'a pas le numéro de période
										$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$lig->id';";
										//echo "$sql<br />\n";
										$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
										$eff_grp=mysqli_num_rows($res_grp);

										// Nombre de notes saisies
										//$sql="SELECT * FROM matieres_notes WHERE id_groupe='$lig->id' AND periode='$lig->valeur';";
										$sql="SELECT DISTINCT ec.* FROM eb_copies ec, 
													eb_groupes eg,
													j_eleves_groupes jeg
												WHERE eg.id_groupe='$lig->id' AND 
													eg.id_groupe=jeg.id_groupe AND 
													jeg.login=ec.login_ele AND 
													eg.id_epreuve=ec.id_epreuve AND 
													eg.id_epreuve='$lig->valeur' AND 
													ec.statut!='v';";
										//echo "$sql<br />\n";
										$res_notes_epb=mysqli_query($GLOBALS["mysqli"], $sql);
										$eff_notes_epb=mysqli_num_rows($res_notes_epb);

										if($eff_notes_epb>0) {
											$texte.="<br />\n";
											$eff_non_note=0;
											$eff_abs=0;
											$eff_disp=0;

											while($lig_nb=mysqli_fetch_object($res_notes_epb)) {
												if($lig_nb->statut=='-') {$eff_non_note++;}
												elseif($lig_nb->statut=='abs') {$eff_abs++;}
												elseif($lig_nb->statut=='disp') {$eff_disp++;}
											}

											if($eff_grp==$eff_notes_epb) {
												$texte.="<span style='color:green;'>$eff_notes_epb/$eff_grp</span>";
											}
											else {
												//$texte.="<span style='color:red;'>$eff_non_vide/$eff_tot</span>";
												$texte.="<span style='color:red;'>$eff_notes_epb/$eff_grp</span>";
											}

											$texte.="<br />\n";
											if($eff_abs>0) {$texte.="$eff_abs absent(s)<br />\n";}
											if($eff_disp>0) {$texte.="$eff_disp dispensé(s)<br />\n";}
											if($eff_non_note>0) {$texte.="$eff_non_note non noté(s)<br />\n";}

											$texte.="<br /><p style='color:red'>ATTENTION&nbsp;: L'effectif indiqué pour le groupe peut être erroné si la liste des élèves a changé au cours des périodes<br />
(<em>on récupère ici la liste de tous les élèves associés au groupe quelle que soit la période</em>).<br />
A améliorer en affichant au moins les effectifs du groupe par période.";
										}
										else {
											$texte.="<span style='color:red;'>Aucune moyenne saisie</span>";
										}

										$tabdiv_infobulle[]=creer_div_infobulle("div_moy_epb_".$lig->id."_".$lig->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
									}

									echo "</span>\n";
								}
								/*
								elseif($lig->id_dev==-1) {
									echo "<br />\n";
									echo "<a href='saisie_notes.php?id_exam=$id_exam&amp;id_groupe=$lig->id&amp;matiere=$tab_matiere[$j]' title='Saisir les notes'>";
									echo "Devoir hors enseignement\n";
									echo "</a>\n";
								}
								*/
								$cpt_grp++;
							}
						}

						//echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;matiere=$tab_matiere[$j]&amp;id_classe=$tab_id_classe[$i]&amp;mode=modif_exam&amp;aff=groupes'>Ajouter des groupes</a></p>\n";


						echo "</td>\n";
					}
					if($grp_hors_enseignement=='y') {
						//echo "<th>Hors enseignements</th>\n";
						echo "<td>\n";
						$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$tab_matiere[$j]' AND id_groupe='0' AND type='hors_enseignement';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)>0) {
							echo "<a href='saisie_notes.php?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$tab_matiere[$j]' title='Notes hors enseignements'";
							echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
							echo ">Notes</a>";

							// Nombre de notes saisies
							$lig_he=mysqli_fetch_object($test);
							$sql="SELECT * FROM ex_notes WHERE id_ex_grp='$lig_he->id';";
							$res_en=mysqli_query($GLOBALS["mysqli"], $sql);
							$eff_tot=mysqli_num_rows($res_en);
							if($eff_tot>0) {
								echo "<br />\n";
								$eff_non_vide=0;
								while($lig_en=mysqli_fetch_object($res_en)) {
									if($lig_en->statut!='v') {$eff_non_vide++;}
								}
								if($eff_non_vide==$eff_tot) {echo "<span style='color:green;'>$eff_non_vide/$eff_tot</span>";}
								else {echo "<span style='color:red;'>$eff_non_vide/$eff_tot</span>";}
							}
							else {
								echo "<span style='color:red;'>Aucune note saisie</span>";
							}
						}
						else {
							echo "&nbsp;";
						}
						echo "</td>\n";
					}
					echo "</tr>\n";
				}

				echo "</table>\n";

				echo "<p align='center'><input type='submit' name='reg_coef' value='Enregistrer les coefficients et bonus' /></p>\n";
				echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
				echo "<input type='hidden' name='mode' value='modif_coef' />\n";

				echo "</form>\n";

			}

			echo "<p><i>NOTES&nbsp;:</i><p>
			<ul>
				<li>
					<p>Les 'bonus' consistent à ne compter que les points supérieurs à 10.<br />
					Ex.: Pour 12 (coef 3), 14 (coef 1) et 13 (coef 2 et bonus), le calcul est (12*3+14*1+(13-10)*2)/(3+1)</p>
				</li>
				<li>
					<p><span style='color:red'>A FAIRE&nbsp;:</span> Pouvoir régler l'ordre des matières.<br />
					L'ordre actuel est alphabétique en faisant passer à la fin les matières à bonus.</p>
				</li>
				<li>
					<p><span style='color:red'>A FAIRE&nbsp;:</span> Présenter le tableau ci-dessus et celui de sélection des devoirs par tranche de 5 ou 6 classes au plus.<br />Ne mettre les coef que sur le premier tableau.<br /></p>
				</li>
			</ul>\n";

			unset($tab_matiere);
			unset($tab_classe);
			unset($tab_id_classe);
			unset($tab_coef);
			unset($tab_bonus);

		}
		//=============================================================================
		elseif($aff=='classes') {
			// Choix des classes

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Examen $id_exam</a>\n";
			echo "</p>\n";

			$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'examen $id_exam n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			//$etat=$lig->etat;

			/*
			if($etat=='clos') {
				echo "<p class='bold'>L'épreuve $id_exam est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			*/

			echo "<p class='bold'>Choix des classes pour l'examen $id_exam&nbsp;:</p>\n";

			if($_SESSION['statut']=='administrateur') {
				$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
			}
			elseif($_SESSION['statut']=='scolarite') {
				$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes j WHERE p.id_classe = c.id AND j.id_classe=c.id ORDER BY classe";
				// Permettre aussi de voir toutes les classes...
			}
			else {
				// Accès prof principal
				$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs jep WHERE jep.id_classe=c.id AND jep.professeur='".$_SESSION['login']."' ORDER BY classe";
			}
			$classes_list = mysqli_query($GLOBALS["mysqli"], $sql);
			$nb = mysqli_num_rows($classes_list);
			if ($nb==0) {
				echo "<p>Aucune classe ne semble définie.</p>\n";
			}
			else {
				// Liste des classes déjà associées à l'examen
				$tab_id_classe=array();
				$sql="SELECT DISTINCT id_classe FROM ex_classes WHERE id_exam='$id_exam';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						$tab_id_classe[]=$lig->id_classe;
					}
				}

				// Choix des classes dont il faudra lister les groupes
				echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
				echo add_token_field();

				$nb_class_par_colonne=round($nb/3);
				echo "<table width='100%' summary='Choix des classes'>\n";
				echo "<tr valign='top' align='center'>\n";
	
				$i=0;
				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";
	
				while ($i < $nb) {
					$id_classe=old_mysql_result($classes_list, $i, 'id');
					//$temp = "id_classe_".$id_classe;
					$classe=old_mysql_result($classes_list, $i, 'classe');
	
					if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$id_classe' ";
					echo "onchange=\"checkbox_change($i);changement();\" ";
					if(in_array($id_classe,$tab_id_classe)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='id_classe_$i'><span id='texte_id_classe_$i'$temp_style>Classe : ".$classe.".</span></label><br />\n";
					$i++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				//echo "<input type='hidden' name='is_posted' value='2' />\n";

				echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
				echo "<input type='hidden' name='mode' value='ajout_classes' />\n";
				//echo "<input type='hidden' name='aff' value='groupes' />\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
				echo "</form>\n";


				echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_classe_'+cpt)) {
		if(document.getElementById('id_classe_'+cpt).checked) {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

			}
		}
		//=============================================================================
		elseif($aff=='matieres') {
			// Choix des matières
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Examen $id_exam</a>\n";
			echo "</p>\n";

			$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'examen $id_exam n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			//$etat=$lig->etat;

			/*
			if($etat=='clos') {
				echo "<p class='bold'>L'épreuve $id_exam est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			*/

			echo "<p class='bold'>Choix des matières pour l'examen $id_exam&nbsp;:</p>\n";

			$sql="SELECT DISTINCT m.* FROM matieres m ORDER BY matiere, nom_complet";
			$matieres_list = mysqli_query($GLOBALS["mysqli"], $sql);
			$nb=mysqli_num_rows($matieres_list);
			if($nb==0) {
				echo "<p>Aucune matières ne semble définie.</p>\n";
			}
			else {
				// Liste des matières déjà associées à l'examen
				$tab_matiere=array();
				$sql="SELECT DISTINCT matiere FROM ex_matieres WHERE id_exam='$id_exam';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						$tab_matiere[]=$lig->matiere;
					}
				}

				// Choix des matières
				echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
				echo add_token_field();

				$nb_matier_par_colonne=round($nb/3);
				echo "<table width='100%' summary='Choix des matières'>\n";
				echo "<tr valign='top' align='center'>\n";
	
				$i=0;
				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";
	
				while($i<$nb) {
					$matiere=old_mysql_result($matieres_list,$i,'matiere');
					//$temp="case_".$matiere;
	
					if(($i>0)&&(round($i/$nb_matier_par_colonne)==$i/$nb_matier_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					//echo "<input type='checkbox' name='matiere[]' id='$temp' value='$matiere' ";
					echo "<input type='checkbox' name='matiere[]' id='matiere_$i' value='$matiere' ";

					echo "onchange=\"checkbox_change($i);changement();\" ";

					if(in_array($matiere,$tab_matiere)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}

					echo "/><label for='matiere_$i'><span id='texte_matiere_$i'$temp_style>Matière : ".$matiere."</span></label><br />\n";
					$i++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				//echo "<input type='hidden' name='is_posted' value='2' />\n";

				echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
				echo "<input type='hidden' name='mode' value='ajout_matieres' />\n";
				//echo "<input type='hidden' name='aff' value='groupes' />\n";
				echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
				echo "</form>\n";

				echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('matiere_'+cpt)) {
		if(document.getElementById('matiere_'+cpt).checked) {
			document.getElementById('texte_matiere_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_matiere_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

			}
		}
		//=============================================================================
		elseif($aff=='groupes') {
			//Choix des groupes

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Examen $id_exam</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam&amp;aff=classes'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Choix des classes</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam&amp;aff=matieres'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Choix des matières</a>\n";
			echo "</p>\n";


			$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'examen $id_exam n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
			$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);

			/*
			if(!isset($id_classe)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'a été choisie.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			*/

			if(!isset($matiere)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune matière n'a été choisie.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			$sql="SELECT DISTINCT ec.id_classe, c.classe FROM ex_classes ec, classes c WHERE c.id=ec.id_classe AND ec.id_exam='$id_exam' ORDER BY c.classe;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig=mysqli_fetch_object($res)) {
				$classe[]=$lig->classe;
				$id_classe[]=$lig->id_classe;
			}

			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			//echo "<p class='bold'>Choix des groupes pour l'examen $id_exam&nbsp;: Classe ".get_class_from_id($id_classe)." et matière $matiere</p>\n";
			echo "<p class='bold'>Choix des groupes pour l'examen $id_exam&nbsp;: Matière $matiere</p>\n";

			$tab_groupes_inscrits=array();
			//$sql="SELECT eg.id_groupe FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$matiere' AND jgc.id_classe='$id_classe' AND jgc.id_groupe=eg.id_groupe;";
			$sql="SELECT eg.id_groupe FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$matiere' AND  jgc.id_groupe=eg.id_groupe;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_groupes_inscrits[]=$lig->id_groupe;
				}
			}

			$groupes_non_visibles['cn']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['cn'][]=$lig_vis->id_groupe;
			}
			$groupes_non_visibles['bull']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['bull'][]=$lig_vis->id_groupe;
			}

			echo "<table class='boireaus' summary='Liste des groupes'>\n";

			echo "<tr>\n";
			for($i=0;$i<count($id_classe);$i++) {
				echo "<th>".get_class_from_id($id_classe[$i])."</th>\n";
			}
			echo "</tr>\n";

			echo "<tr>\n";
			$alt=1;
			$cpt=0;
			for($i=0;$i<count($id_classe);$i++) {
				$alt=$alt*(-1);
				echo "<td class='lig$alt' style='text-align:left; vertical-align:top;'>\n";
				$sql="SELECT g.* FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe=g.id AND jgc.id_classe='$id_classe[$i]' AND jgm.id_matiere='$matiere' AND jgm.id_groupe=jgc.id_groupe ORDER BY g.name;";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						if((!in_array($lig->id, $groupes_non_visibles['cn']))||(!in_array($lig->id, $groupes_non_visibles['bull']))) {
							echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='$lig->id' ";
							echo "onchange=\"checkbox_change($cpt);changement();\" ";
							if(in_array($lig->id,$tab_groupes_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
							echo "/><label for='id_groupe_$cpt' style='cursor: pointer;'><span id='texte_id_groupe_$cpt' $temp_style>".htmlspecialchars($lig->name)." (<span style='font-style:italic;font-size:x-small;'>".htmlspecialchars($lig->description)."</span>)</span></label><br />\n";
							$cpt++;
						}
					}
				}
				else {
					// Proposer d'associer sans enseignement
					// PB: Si on met tous les élèves de la classe???... ne pas faire apparaitre sur le 'bulletin' les notes sans...
					echo "Aucun groupe";
/*
					echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='$lig->id' ";
					echo "onchange=\"checkbox_change($cpt)\" ";
					if(in_array($lig->id,$tab_groupes_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='id_groupe_$cpt' style='cursor: pointer;'><span id='texte_id_groupe_$cpt' $temp_style>".htmlspecialchars($lig->name)." (<span style='font-style:italic;font-size:x-small;'>".htmlspecialchars($lig->description)."</span>)</span></label><br />\n";
					$cpt++;
*/
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
			echo "</table>\n";

			echo "<p>\n";
			echo "<input type='checkbox' name='groupe_hors_enseignement' id='groupe_hors_enseignement' value='y' onchange='changement()' ";
			$sql="SELECT 1=1 FROM ex_groupes WHERE id_exam='$id_exam' AND matiere='$matiere' AND id_groupe='0' AND type='hors_enseignement';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				echo "checked ";
			}
			echo "/>\n";
			echo "<label for='groupe_hors_enseignement' style='cursor: pointer;'> Créer un groupe hors enseignement</label><br />\n";
			echo "(<i>Un tel 'groupe' permet par exemple de saisir en terminale des notes obtenues au bac de français en première</i>).</p>\n";

			echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
			echo "<input type='hidden' name='matiere' value='$matiere' />\n";
			//echo "<input type='hidden' name='mode' value='modif_exam' />\n";
			echo "<input type='hidden' name='mode' value='ajout_groupes' />\n";
			//echo "<input type='hidden' name='aff' value='groupes' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p><a href='javascript:cocher_decocher(true)'>Cocher</a> / <a href='javascript:cocher_decocher(false)'>décocher</a> tous les groupes.</p>\n";

			echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_groupe_'+cpt)) {
		if(document.getElementById('id_groupe_'+cpt).checked) {
			document.getElementById('texte_id_groupe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_groupe_'+cpt).style.fontWeight='normal';
		}
	}
}

// Tout cocher/décocher
function cocher_decocher(mode) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('id_groupe_'+k)){
			document.getElementById('id_groupe_'+k).checked=mode;
		}
	}
}

</script>\n";

			echo "<p><br /></p>\n";
			echo "<p><i>Remarque à propos des notes hors enseignement&nbsp;:</i> On crée normalement un 'groupe' hors enseignement pour une note devant être prise en compte alors qu'elle ne correspond pas à un enseignement dispensé sur l'année (<i>exemple: note de français pour le BAC</i>).<br />On ne devrait pas alors avoir de situation bizarre comme celle proposée/commentée ci-dessous&nbsp;:<br />Si un élève est inscrit pour une même matière à la fois avec un devoir (<i>ou une moyenne de bulletin</i>) et avec une note hors enseignement, c'est la note hors enseignement qui est prise en compte.</p>\n";
			echo "<p><br /></p>\n";

		}
		//=============================================================================
		elseif($aff=='choix_dev') {
			//Choix des devoirs
			$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Examen $id_exam</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam&amp;aff=classes'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Choix des classes</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam&amp;aff=matieres'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Choix des matières</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_exam&amp;id_exam=$id_exam&amp;matiere=$matiere&amp;aff=groupes'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Choix des groupes</a>\n";
			echo "</p>\n";

			$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'examen $id_exam n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			if(!isset($matiere)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune matière n'a été choisie.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			//$sql="SELECT DISTINCT ec.id_classe, c.classe FROM ex_classes ec, classes c WHERE c.id=ec.id_classe AND ec.id_exam='$id_exam' ORDER BY c.classe;";
			$sql="SELECT DISTINCT ec.id_classe, c.classe FROM ex_classes ec, ex_groupes eg, classes c, j_groupes_classes jgc WHERE c.id=ec.id_classe AND ec.id_exam='$id_exam' AND eg.id_exam=ec.id_exam AND eg.matiere='$matiere' AND jgc.id_classe=ec.id_classe AND jgc.id_groupe=eg.id_groupe ORDER BY c.classe;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$id_classe=array();
			$classe=array();
			while($lig=mysqli_fetch_object($res)) {
				$classe[]=$lig->classe;
				$id_classe[]=$lig->id_classe;
			}
			$nb_classes=count($id_classe);

			if($nb_classes==0) {
				echo "<p style='color:red'>Aucune classe n'a été choisie.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			echo "<p class='bold'>Choix des évaluations ou moyennes pour l'examen $id_exam&nbsp;: Classe";
			if($nb_classes>1) {
				echo "s";
			}
			echo " ";
			for($loop=0;$loop<count($id_classe);$loop++) {
				if($loop>0) {echo ", ";}
				echo get_class_from_id($id_classe[$loop]);
			}
			echo " et matière $matiere</p>\n";

			$groupes_non_visibles['cn']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['cn'][]=$lig_vis->id_groupe;
			}
			$groupes_non_visibles['bull']=array();
			$sql="SELECT DISTINCT id_groupe FROM j_groupes_visibilite WHERE domaine='bulletins' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_vis=mysqli_fetch_object($res_vis)) {
				$groupes_non_visibles['bull'][]=$lig_vis->id_groupe;
			}

			$tab_moy_bull_inscrits=array();
			$tab_moy_pp_inscrits=array();
			$tab_dev_inscrits=array();
			$tab_moy_epb_inscrits=array();
			//$sql="SELECT eg.id_dev FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$matiere';";
			$sql="SELECT eg.id_dev,eg.id_groupe,eg.type,eg.valeur FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$matiere';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					if($lig->type=='moy_bull') {
						$tab_moy_bull_inscrits[$lig->id_groupe]=$lig->valeur;
					}
					elseif($lig->type=='moy_plusieurs_periodes') {
						//$tab_moy_pp_inscrits[$lig->id_groupe]=$lig->valeur;
						$tab_tmp=explode(" ", $lig->valeur);
						for($loop=0;$loop<count($tab_tmp);$loop++) {
							if((!isset($tab_moy_pp_inscrits[$lig->id_groupe]))||(!in_array($tab_tmp[$loop],$tab_moy_pp_inscrits[$lig->id_groupe]))) {
								$tab_moy_pp_inscrits[$lig->id_groupe][]=$tab_tmp[$loop];
							}
						}
					}
					elseif($lig->type=='epreuve_blanche') {
						$tab_moy_epb_inscrits[$lig->id_groupe]=$lig->valeur;
					}
					elseif($lig->type=='') {
						$tab_dev_inscrits[]=$lig->id_dev;
					}
				}
			}

			echo "<table class='boireaus' summary='Liste des évaluations/moyennes'>\n";

			echo "<tr>\n";
			for($i=0;$i<$nb_classes;$i++) {
				echo "<th>".get_class_from_id($id_classe[$i])."</th>\n";
			}
			echo "</tr>\n";

			echo "<tr>\n";
			$alt=1;
			$cpt=0;
			$cpt_grp=0;
			for($i=0;$i<$nb_classes;$i++) {
				$alt=$alt*(-1);
				echo "<td class='lig$alt' style='text-align:left; vertical-align:top;'>\n";
				$sql="SELECT g.* FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe=g.id AND jgc.id_classe='$id_classe[$i]' AND jgm.id_matiere='$matiere' AND jgm.id_groupe=jgc.id_groupe AND g.id IN (SELECT id_groupe FROM ex_groupes WHERE id_exam='$id_exam') ORDER BY g.name;";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						if((!in_array($lig->id, $groupes_non_visibles['cn']))||(!in_array($lig->id, $groupes_non_visibles['bull']))) {
							echo "<p><span class='bold'>".htmlspecialchars($lig->name)." (<span style='font-style:italic;font-size:x-small;'>".htmlspecialchars($lig->description)."</span>)</span><br />\n";

							echo "<input type='hidden' name='id_groupe[$cpt_grp]' value='$lig->id' />\n";

							/*
							// Le dispositif commenté ici aurait seulement permis de saisir un devoir pour un groupe existant alors que ce qui est utile, c'est de pouvoir créer un enseignement pour le bac de français, note obtenue en première.
							echo "<input type='radio' name='id_dev_".$cpt_grp."' id='id_dev_".$cpt_grp."_$cpt' value='-1' ";
							echo "onchange=\"radio_change($cpt_grp,$cpt)\" ";
							//if() {echo "checked ";}
							echo "/><label for='id_dev_".$cpt_grp."_$cpt' style='cursor: pointer;'>";
							echo "Saisir une <span id='texte_id_dev_".$cpt_grp."_$cpt'>série de notes hors enseignement de l'année</span>\n";
							echo "</label><br />\n";
							$cpt++;

							echo "ou choisir un devoir existant&nbsp;:<br />\n";
							*/

							$sql="SELECT cd.*, ccn.periode FROM cn_devoirs cd, cn_cahier_notes ccn WHERE ccn.id_groupe='$lig->id' AND ccn.id_cahier_notes=cd.id_racine ORDER BY ccn.periode, cd.date, cd.nom_court, cd.nom_complet;";
							//echo "$sql<br />\n";
							$res2=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res2)>0) {
								$periode_precedente=-1;
								while($lig2=mysqli_fetch_object($res2)) {
									if($lig2->periode!=$periode_precedente) {
										unset($lig3);

										if(!in_array($lig->id, $groupes_non_visibles['bull'])) {
											echo "<label for='id_dev_".$cpt_grp."_$cpt' style='cursor: pointer;' alt='Moyenne du bulletin pour la période' title='Moyenne du bulletin pour la période'>";
											$sql="SELECT nom_periode, verouiller FROM periodes WHERE id_classe='$id_classe[$i]' AND num_periode='$lig2->periode';";
											$res3=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res3)>0) {
												$lig3=mysqli_fetch_object($res3);
												echo "<span class='bold'>".htmlspecialchars($lig3->nom_periode)."</span>\n";

												$tab_periodes[$cpt_grp][]=$lig2->periode;
											}
											else {
												// Ca ne devrait pas arriver...
												echo "<span class='bold'>Période ".$lig2->periode."</span>\n";

												$tab_periodes[$cpt_grp][]=$lig2->periode;
											}
											echo "</label>\n";

											echo "&nbsp;<input type='radio' name='id_dev_".$cpt_grp."' id='id_dev_".$cpt_grp."_$cpt' value='P$lig2->periode' ";
											echo "onchange=\"radio_change($cpt_grp,$cpt);changement();\" ";

											if((isset($tab_moy_bull_inscrits[$lig->id]))&&($tab_moy_bull_inscrits[$lig->id]==$lig2->periode)) {
												echo "checked ";
											}

											echo "/>";

											if(isset($lig3)) {
												if($lig3->verouiller=='N') {echo "<img src='../images/icons/flag.png' width='17' height='18' alt='ATTENTION: Période non close' title='ATTENTION: Période non close' />\n";}
											}
										}
										else {
											echo "<span title='Moyenne du bulletin pour la période'>";
											$sql="SELECT nom_periode, verouiller FROM periodes WHERE id_classe='$id_classe[$i]' AND num_periode='$lig2->periode';";
											$res3=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res3)>0) {
												$lig3=mysqli_fetch_object($res3);
												echo "<span class='bold'>".htmlspecialchars($lig3->nom_periode)."</span>\n";

												$tab_periodes[$cpt_grp][]=$lig2->periode;
											}
											else {
												// Ca ne devrait pas arriver...
												echo "<span class='bold'>Période ".$lig2->periode."</span>\n";

												$tab_periodes[$cpt_grp][]=$lig2->periode;
											}
											echo "</span>\n";
										}
										echo "<br />\n";
										$periode_precedente=$lig2->periode;

										$cpt++;
									}

									echo "<input type='radio' name='id_dev_".$cpt_grp."' id='id_dev_".$cpt_grp."_$cpt' value='$lig2->id' ";
									echo "onchange=\"radio_change($cpt_grp,$cpt);changement();\" ";
									if(in_array($lig2->id,$tab_dev_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
									echo "/><label for='id_dev_".$cpt_grp."_$cpt' style='cursor: pointer;'><span id='texte_id_dev_".$cpt_grp."_$cpt' $temp_style>".htmlspecialchars($lig2->nom_court)." (<span style='font-style:italic;font-size:x-small;'>".formate_date($lig2->date)."</span>)</span></label><br />\n";

									$cpt++;
								}
							}
							else {
								echo "Aucun devoir.<br />";
							}

							if(!in_array($lig->id, $groupes_non_visibles['bull'])) {
								$sql="SELECT DISTINCT mn.periode, p.nom_periode, p.verouiller FROM matieres_notes mn, periodes p WHERE mn.id_groupe='$lig->id' AND p.id_classe='$id_classe[$i]' AND p.num_periode=mn.periode ORDER BY periode;";
								//echo "$sql<br />\n";
								$res2=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res2)>0) {
									while($lig2=mysqli_fetch_object($res2)) {
										if((!isset($tab_periodes[$cpt_grp]))||(!in_array($lig2->periode, $tab_periodes[$cpt_grp]))) {
											echo "<label for='id_dev_".$cpt_grp."_$cpt' style='cursor: pointer;' alt='Moyenne du bulletin pour la période' title='Moyenne du bulletin pour la période'>";
											echo "<span class='bold'>".htmlspecialchars($lig2->nom_periode)."</span>\n";
											$tab_periodes[$cpt_grp][]=$lig2->periode;
											echo "</label>\n";
											echo "&nbsp;<input type='radio' name='id_dev_".$cpt_grp."' id='id_dev_".$cpt_grp."_$cpt' value='P$lig2->periode' ";
											echo "onchange=\"radio_change($cpt_grp,$cpt);changement();\" ";
											if((isset($tab_moy_bull_inscrits[$lig->id]))&&($tab_moy_bull_inscrits[$lig->id]==$lig2->periode)) {
												echo "checked ";
											}
											echo "/>";

											if($lig2->verouiller=='N') {echo "<img src='../images/icons/flag.png' width='17' height='18' alt='ATTENTION: Période non close' title='ATTENTION: Période non close' />\n";}
											echo "<br />\n";

											$cpt++;
										}
									}
								}
							}


							// Et proposer de saisir des notes hors devoir


							/*
								echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='$lig->id' ";
								echo "onchange=\"checkbox_change($cpt)\" ";
								if(in_array($lig->id,$tab_groupes_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
								echo "/><label for='id_groupe_$cpt' style='cursor: pointer;'><span id='texte_id_groupe_$cpt' $temp_style>".htmlspecialchars($lig->name)." (<span style='font-style:italic;font-size:x-small;'>".htmlspecialchars($lig->description)."</span>)</span></label><br />\n";
							*/


							if(isset($tab_periodes[$cpt_grp])) {
								//echo "<hr />\n";
								echo "<b>Ou moyenne(s) de période(s)</b><br />\n";
								//echo "<b>Moyenne de plusieurs périodes:</b>\n";
								echo "<input type='radio' name='id_dev_".$cpt_grp."' id='id_dev_".$cpt_grp."_$cpt' value='Plusieurs_periodes' ";
								echo "onchange=\"radio_change($cpt_grp,$cpt);changement();\" ";

								//if(in_array($lig2->id,$tab_dev_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
								if(isset($tab_moy_pp_inscrits[$lig->id])) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}

								echo "/><label for='id_dev_".$cpt_grp."_$cpt' style='cursor: pointer;'><span id='texte_id_dev_".$cpt_grp."_$cpt' $temp_style>Moyenne de plusieurs périodes&nbsp;:</span></label><br />\n";

								for($j=0;$j<count($tab_periodes[$cpt_grp]);$j++) {
									echo "<span style='margin-left:2em;'><input type='checkbox' name='id_dev_".$cpt_grp."_periodes[]' id='id_dev_".$cpt_grp."_periodes_$j' value='".$tab_periodes[$cpt_grp][$j]."' onchange=\"document.getElementById('id_dev_".$cpt_grp."_$cpt').checked=true;\" ";
									if((isset($tab_moy_pp_inscrits[$lig->id]))&&(in_array($tab_periodes[$cpt_grp][$j],$tab_moy_pp_inscrits[$lig->id]))) {echo "checked ";}
									echo "/><label for='id_dev_".$cpt_grp."_periodes_$j'>Période ".$tab_periodes[$cpt_grp][$j]."</label></span><br />\n";
								}
								$cpt++;
							}

							// Choisir une épreuve blanche
							$sql="SELECT ee.* FROM eb_epreuves ee, eb_groupes eg WHERE ee.id=eg.id_epreuve AND eg.id_groupe='$lig->id';";
							//echo "$sql<br />\n";
							$res_epreuve=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)>0) {
								echo "<b>Ou épreuve blanche</b>&nbsp;:<br />";
								while($lig_epreuve=mysqli_fetch_object($res_epreuve)) {
									echo "<input type='radio' name='id_dev_".$cpt_grp."' id='id_dev_".$cpt_grp."_$cpt' value='epb_".$lig_epreuve->id."' ";
									echo "onchange=\"radio_change($cpt_grp,$cpt);changement();\" ";

									if((isset($tab_moy_epb_inscrits[$lig->id]))&&($tab_moy_epb_inscrits[$lig->id]==$lig_epreuve->id)) {
										echo "checked ";
										$temp_style="style='font-weight:bold;'";
									}
									else {
										$temp_style="";
									}

									echo "/><label for='id_dev_".$cpt_grp."_$cpt' style='cursor: pointer;'><span id='texte_id_dev_".$cpt_grp."_$cpt' $temp_style title=\"$lig_epreuve->description
Date     : ".formate_date($lig_epreuve->date)."
Note sur : $lig_epreuve->note_sur\">$lig_epreuve->intitule</span></label><br />\n";
									$cpt++;
								}
							}
						}

						$cpt_grp++;
					}
				}

				echo "</td>\n";
			}
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
			echo "<input type='hidden' name='matiere' value='$matiere' />\n";
			//echo "<input type='hidden' name='mode' value='modif_exam' />\n";
			echo "<input type='hidden' name='mode' value='modif_choix_dev' />\n";
			//echo "<input type='hidden' name='aff' value='groupes' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p><em>NOTES&nbsp;:</em></p>\n";
			echo "<ul><li>Dans le cas où vous choisissez une moyenne de plusieurs périodes, le calcul de la moyenne des différentes périodes est faite sur le champ.<br />Si les notes sont modifiées par la suite par les professeurs, les modifications ne sont prises en compte que si vous revalidez le présent formulaire.</li></ul>\n";

			echo "<script type='text/javascript'>
function radio_change(i,cpt) {
	for(j=0;j<$cpt;j++) {
		if(document.getElementById('id_dev_'+i+'_'+j)) {
			if(document.getElementById('texte_id_dev_'+i+'_'+j)) {
				document.getElementById('texte_id_dev_'+i+'_'+j).style.fontWeight='normal';
			}
		}
	}

	if(document.getElementById('id_dev_'+i+'_'+cpt)) {
		if(document.getElementById('texte_id_dev_'+i+'_'+cpt)) {
			if(document.getElementById('id_dev_'+i+'_'+cpt).checked) {
				document.getElementById('texte_id_dev_'+i+'_'+cpt).style.fontWeight='bold';
			}
			else {
				document.getElementById('texte_id_dev_'+i+'_'+cpt).style.fontWeight='normal';
			}
		}
	}
}
</script>\n";

		}
		//=============================================================================
	}
}
//=============================================================================

//echo "<span style='color:red;'>ALTER TABLE ex_groupes ADD valeur VARCHAR( 255 ) NOT NULL ;</span> à mettre en utilitaires/updates/152_to_153.inc.php et sql/structure_gepi.sql";

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
