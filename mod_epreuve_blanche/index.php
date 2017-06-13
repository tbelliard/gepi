<?php
/*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/index.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Accueil',
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

include('lib_eb.php');

//=========================================================

// Création des tables

$sql="CREATE TABLE IF NOT EXISTS eb_epreuves (
id int(11) unsigned NOT NULL auto_increment,
intitule VARCHAR( 255 ) NOT NULL ,
description TEXT NOT NULL ,
type_anonymat VARCHAR( 255 ) NOT NULL ,
date DATE NOT NULL default '0000-00-00',
etat VARCHAR( 255 ) NOT NULL ,
note_sur int(11) unsigned not null default '20',
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS eb_copies (
id int(11) unsigned NOT NULL auto_increment,
login_ele VARCHAR( 255 ) NOT NULL ,
n_anonymat VARCHAR( 255 ) NOT NULL,
id_salle INT( 11 ) NOT NULL default '-1',
login_prof VARCHAR( 255 ) NOT NULL ,
note float(10,1) NOT NULL default '0.0',
statut VARCHAR(255) NOT NULL default '',
id_epreuve int(11) unsigned NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS eb_salles (
id int(11) unsigned NOT NULL auto_increment,
salle VARCHAR( 255 ) NOT NULL ,
id_epreuve int(11) unsigned NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS eb_groupes (
id int(11) unsigned NOT NULL auto_increment,
id_epreuve int(11) unsigned NOT NULL,
id_groupe int(11) unsigned NOT NULL,
transfert varchar(1) NOT NULL DEFAULT 'n',
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS eb_profs (
id int(11) unsigned NOT NULL auto_increment,
id_epreuve int(11) unsigned NOT NULL,
login_prof VARCHAR(255) NOT NULL default '',
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
//echo "$sql<br />";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

//=========================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$id_epreuve_modele=isset($_POST['id_epreuve_modele']) ? $_POST['id_epreuve_modele'] : (isset($_GET['id_epreuve_modele']) ? $_GET['id_epreuve_modele'] : NULL);


//$modif_epreuve=isset($_POST['modif_epreuve']) ? $_POST['modif_epreuve'] : (isset($_GET['modif_epreuve']) ? $_GET['modif_epreuve'] : NULL);

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {

	if(isset($id_epreuve)) {
		$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$msg="L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.\n";
		}
		else {
			$lig=mysqli_fetch_object($res);
			$etat=$lig->etat;
		
			if($etat=='clos') {
				/*
				if((isset($mode))&&($mode=='declore')) {

				}
				*/
				if(isset($_POST['modif_epreuve'])) {unset($_POST['modif_epreuve']);}
				//if((isset($mode))&&($mode!='clore')&&($mode!='declore')&&($mode!='modif_epreuve')) {$mode=NULL;}
				if((isset($mode))&&($mode!='clore')&&($mode!='declore')&&($mode!='modif_epreuve')&&($mode!='copier_choix')) {$mode=NULL;}
			}
		}
	}

	// Témoin d'une modification de numéros anonymat (pour informer qu'il faut regénérer les étiquettes,...)
	$temoin_n_anonymat='n';
	// Témoin d'une erreur anonymat pour un élève au moins
	$temoin_erreur_n_anonymat='n';

	//if(isset($_POST['creer_epreuve'])) {
	if((isset($_POST['creer_epreuve']))||(isset($_POST['modif_epreuve']))) {
		check_token();
		$msg="";

		// Correction, modification des paramètres d'une épreuve

		$intitule=isset($_POST['intitule']) ? $_POST['intitule'] : "Epreuve blanche";
		$date=isset($_POST['date']) ? $_POST['date'] : "";
		$description=isset($_POST['description']) ? $_POST['description'] : "";
		$type_anonymat=isset($_POST['type_anonymat']) ? $_POST['type_anonymat'] : "ele_id";
		$note_sur=isset($_POST['note_sur']) ? $_POST['note_sur'] : 20;
		if(!preg_match('/^[0-9]*$/',$note_sur)) {
			$note_sur=20;
			$msg.="Valeur de note_sur invalide<br />";
		}

		if(mb_strlen(preg_replace("/[A-Za-z0-9 _\.-]/","",remplace_accents($intitule,'all')))!=0) {$intitule=preg_replace("/[^A-Za-zÂÄÀÁÃÄÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõ¨ûüùúýÿ¸0-9_.-]/"," ",$intitule);}
		if($intitule=="") {$intitule="Epreuve blanche";}

		$tab_anonymat=array('elenoet','ele_id','no_gep','alea','chrono');
		if(!in_array($type_anonymat,$tab_anonymat)) {$type_anonymat="ele_id";}

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

		if(!isset($id_epreuve)) {
			//$sql="INSERT INTO eb_epreuves SET intitule='$intitule', description='".addslashes($description)."', type_anonymat='$type_anonymat', date='', etat='';";
			$sql="INSERT INTO eb_epreuves SET intitule='$intitule', description='$description', type_anonymat='$type_anonymat', date='$date', etat='', note_sur='$note_sur';";
			if($insert=mysqli_query($GLOBALS["mysqli"], $sql)) {
				$id_epreuve=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
				$msg.="Epreuve n°$id_epreuve : '$intitule' créée.<br />";
			}
			else {
				$msg.="ERREUR lors de la création de l'épreuve '$intitule'.<br />";
				//$msg.="<br />$sql";
			}
		}
		else {
			//********************************************
			// A FAIRE: POUVOIR INTERDIRE OU ALERTER SUR LA MODIFICATION DU TYPE_ANONYMAT UNE FOIS LES LISTINGS/ETIQUETTES GENERES
			//********************************************

			$sql="SELECT type_anonymat FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$lig=mysqli_fetch_object($res);
			$old_type_anonymat=$lig->type_anonymat;

			$sql="UPDATE eb_epreuves SET intitule='$intitule', description='$description', type_anonymat='$type_anonymat', date='$date', note_sur='$note_sur' WHERE id='$id_epreuve';";
			if($update=mysqli_query($GLOBALS["mysqli"], $sql)) {
				$msg.="Epreuve n°$id_epreuve : '$intitule' mise à jour.";

				if($type_anonymat!=$old_type_anonymat) {
					$tab_n_anonymat_affectes=array();

					// On commence par vider les numéros d'anonymat avant de refaire l'affectation
					$sql="UPDATE eb_copies SET n_anonymat='' WHERE id='$id_epreuve';";
					//echo "$sql<br />";
					$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

					// Mettre à jour le type anonymat pour les copies déjà inscrites
					// ERic : Ajout du order by pour numéroter dans l'ordre alphabétique (Le id_elv est là en cas d'homonyme)
					$sql="SELECT e.* FROM eb_copies ec, eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login order by e.nom, e.prenom, e.id_eleve;";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					$cpt_ano = 1;
					while($lig=mysqli_fetch_object($res)) {
						// Témoin d'une erreur anonymat pour l'élève courant
						$temoin_erreur="n";
						if($type_anonymat=='alea') {
							$n_anonymat=chaine_alea(3,4);
							while(in_array($n_anonymat,$tab_n_anonymat_affectes)) {$n_anonymat=chaine_alea(3,4);}
							$tab_n_anonymat_affectes[]=$n_anonymat;
							
						} else if ($type_anonymat=='chrono'){// Eric Ajout du numéro d'anonymat chronologique
							 $n_anonymat='MC'.sprintf("%05s",$cpt_ano); //MC00nnn
							$tab_n_anonymat_affectes[]=$n_anonymat;
							$cpt_ano += 1;		
						}
						else {
							$n_anonymat=$lig->$type_anonymat;
							if(in_array($n_anonymat,$tab_n_anonymat_affectes)) {
								$msg.="Erreur: Le numéro '$n_anonymat' de $lig->login est déjà affecté à un autre élève.<br />";
								$temoin_erreur="y";
								$temoin_erreur_n_anonymat="y";
							}
							$tab_n_anonymat_affectes[]=$n_anonymat;
						}
						
						if($temoin_erreur=="n") {
							$sql="UPDATE eb_copies SET n_anonymat='$n_anonymat' WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if($update) {
								$temoin_n_anonymat='y';
							}
						}
					}
				}
			}
			else {
				$msg.="ERREUR lors de la modification de l'épreuve '$intitule'.";
				//$msg.="<br />$sql";
			}
		}
		$mode="modif_epreuve";
	}
	elseif((isset($id_epreuve))&&($mode=='suppr_epreuve')) {
		check_token();

		// Suppression d'une épreuve
		//echo "gloups";
		//$tab_tables=array('eb_profs', 'eb_salles', 'eb_groupes', 'eb_copies', 'eb_epreuves');
		$tab_tables=array('eb_profs', 'eb_salles', 'eb_groupes', 'eb_copies');
		for($i=0;$i<count($tab_tables);$i++) {
			//$sql="DELETE FROM eb_epreuves WHERE id='$id_epreuve';";
			$sql="DELETE FROM $tab_tables[$i] WHERE id_epreuve='$id_epreuve';";
			$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$suppr) {
				$msg="ERREUR lors de la suppression de l'épreuve $id_epreuve";
				//for($j=0;$j<$i;$j++) {$msg.=""}
				unset($id_epreuve);
				unset($mode);
				break;
			}
		}
		if($msg=='') {
			$sql="DELETE FROM eb_epreuves WHERE id='$id_epreuve';";
			$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$suppr) {
				$msg="ERREUR lors de la suppression de l'épreuve $id_epreuve";
			}
			else {
				$msg="Suppression de l'épreuve $id_epreuve effectuée.";
			}
		}
		unset($id_epreuve);
		unset($mode);
	}
	elseif((isset($id_epreuve))&&($mode=='ajout_groupes')) {
		check_token();

		// Ajout de groupes pour l'épreuve sélectionnée
		$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : array());

		$sql="DELETE FROM eb_groupes WHERE id_epreuve='$id_epreuve';";
		$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$suppr) {
			$msg="ERREUR lors de la réinitialisation des groupes inscrits.";
		}
		else {
			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg="L'épreuve n°$id_epreuve n'existe pas.<br />";
			}
			else {
				$lig=mysqli_fetch_object($res);
				$type_anonymat=$lig->type_anonymat;
				$tab_anonymat=array('elenoet','ele_id','no_gep','alea','chrono');
				if(!in_array($type_anonymat,$tab_anonymat)) {$type_anonymat="ele_id";}

				// On ne supprime que les enregistrements de copies pour lesquelles aucune note n'est encore saisie
				$sql="DELETE FROM eb_copies WHERE id_epreuve='$id_epreuve' AND statut='v';";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);

				$tab_n_anonymat_affectes=array();
				$sql="SELECT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						$tab_n_anonymat_affectes[]=$lig->n_anonymat;
					}
				}

				$cpt_ano = 1;
				if ($type_anonymat=='chrono') {
					$sql="SELECT n_anonymat FROM eb_copies WHERE id_epreuve='$id_epreuve' ORDER BY n_anonymat DESC LIMIT 1;";
					$res_max_anonymat=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysql_num_rows($res_max_anonymat)>0) {
						$lig_max_anonymat=mysql_fetch_object($res_max_anonymat);
						$cpt_ano=preg_replace("/^0*/", "", preg_replace("/^MC/", "", $lig_max_anonymat->n_anonymat));
					}
				}
				

				$msg="";
				for($i=0;$i<count($id_groupe);$i++) {
					$sql="INSERT INTO eb_groupes SET id_epreuve='$id_epreuve', id_groupe='$id_groupe[$i]';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout du groupe n°$id_groupe[$i]<br />";
					}

					if(($type_anonymat=='alea') ||($type_anonymat=='chrono')) {
						//$sql="SELECT DISTINCT login FROM j_eleves_groupes WHERE id_groupe='$id_groupe[$i]';";
						$sql="SELECT DISTINCT j.login, e.date_sortie FROM j_eleves_groupes j, eleves e WHERE j.id_groupe='$id_groupe[$i]' AND j.login=e.login AND (e.date_sortie='0000-00-00 00:00:00' OR e.date_sortie IS NULL);";
						//echo "$sql<br />\n";
					}
					else {
						//$sql="SELECT DISTINCT j.login,e.$type_anonymat FROM j_eleves_groupes j, eleves e WHERE j.id_groupe='$id_groupe[$i]' AND j.login=e.login;";
						$sql="SELECT DISTINCT j.login,e.$type_anonymat, e.date_sortie FROM j_eleves_groupes j, eleves e WHERE j.id_groupe='$id_groupe[$i]' AND j.login=e.login AND (e.date_sortie='0000-00-00 00:00:00' OR e.date_sortie IS NULL);";
						//echo "$sql<br />\n";
					}
					// Il faudra voir comment gérer le cas d'élèves partis en cours d'année... faire choisir la période?
					// Eric le 9-4-11 ==> utilisation de la date de sortie pour l'élève. Elève présent ==> date_sortie=0 ou null
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					$cpt_ano = 1;
					while($lig=mysqli_fetch_object($res)) {
						$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							if($type_anonymat=='alea') {
								$n_anonymat=chaine_alea(3,4);
								while(in_array($n_anonymat,$tab_n_anonymat_affectes)) {$n_anonymat=chaine_alea(3,4);}
								$tab_n_anonymat_affectes[]=$n_anonymat;
							} else if ($type_anonymat=='chrono'){
								// Eric Ajout du numéro d'anonymat chronologique
								$n_anonymat='MC'.sprintf("%05s",$cpt_ano); //MC00nnn
								$tab_n_anonymat_affectes[]=$n_anonymat;
								$cpt_ano += 1;
							}
							else {
								$n_anonymat=$lig->$type_anonymat;
								if(in_array($n_anonymat,$tab_n_anonymat_affectes)) {$msg.="Erreur: Le numéro '$n_anonymat' de $lig->login est déjà affecté à un autre élève.<br />";}
								$tab_n_anonymat_affectes[]=$n_anonymat;
							}
							
							$sql="INSERT INTO eb_copies SET id_epreuve='$id_epreuve', login_ele='$lig->login', n_anonymat='$n_anonymat', statut='v';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$insert) {
								$msg.="Erreur lors de l'ajout de l'élève $lig->login<br />";
							}
							else {
								$temoin_n_anonymat='y';
							}
						}
					}
				}
				if($msg=='') {$msg="Ajout de(s) groupe(s) effectué.<br />";}
			}
		}
		$mode='modif_epreuve';
	}
	elseif((isset($id_epreuve))&&($mode=='suppr_groupe')) {
		check_token();

		// Ajout de groupes pour l'épreuve sélectionnée
		$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;

		if(isset($id_groupe)) {
			$sql="SELECT 1=1 FROM eb_copies ec, eb_groupes eg WHERE ec.id_epreuve='$id_epreuve' AND eg.id_epreuve='$id_epreuve' AND eg.id_groupe='$id_groupe' AND statut!='v';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==1) {
				$msg="Une note a déjà été saisie pour une copie associée au groupe.";
			}
			elseif(mysqli_num_rows($test)>1) {
				$msg=mysqli_num_rows($test)." notes ont déjà été saisies pour des copies associées au groupe.";
			}
			else {
				//$sql="DELETE FROM eb_copies ec, eb_groupes eg WHERE ec.id_epreuve='$id_epreuve' AND eg.id_epreuve=ec.id_epreuve AND eg.id_groupe='$id_groupe';";

				//$sql="SELECT ec.id FROM eb_copies ec, j_eleves_groupes jeg, eb_groupes WHERE ec.login_ele=jeg.login AND jeg.id_groupe=eg.id_groupe AND eg.id_groupe='$id_groupe' AND ec.id_epreuve='$id_epreuve' AND eg.id_epreuve=ec.id_epreuve;";

				// Vérifier qu'il n'y a pas plusieurs groupes avec un même élève
				$nb_err_suppr=0;
				$sql="SELECT DISTINCT ec.login_ele FROM eb_copies ec, j_eleves_groupes jeg, eb_groupes eg WHERE ec.login_ele=jeg.login AND jeg.id_groupe=eg.id_groupe AND eg.id_groupe='$id_groupe' AND ec.id_epreuve='$id_epreuve' AND eg.id_epreuve=ec.id_epreuve;";
				//echo "<p>$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						// Pour ne pas supprimer un élève passé d'un groupe à un autre lors d'un changement de classe
						//$sql="SELECT DISTINCT eg.id_groupe FROM eb_groupes eg, j_eleves_groupes jeg WHERE jeg.id_groupe=eg.id_groupe AND eg.id_groupe='$id_groupe' AND jeg.login='$lig->login_ele';";

						$sql="SELECT DISTINCT eg.id_groupe FROM eb_groupes eg, j_eleves_groupes jeg WHERE eg.id_groupe!='$id_groupe' AND eg.id_groupe=jeg.id_groupe AND jeg.login='$lig->login_ele' AND eg.id_epreuve='$id_epreuve';";
						//echo "$sql<br />";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "mysql_num_rows(\$test)=".mysql_num_rows($test)."<br />";
						if(mysqli_num_rows($test)==0) {
							$sql="DELETE FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
							//echo "$sql<br />";
							$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$suppr) {$nb_err_suppr++;}
						}
					}
				}
				// DEBUG: Décommenter pour bloquer les suppressions de groupes:
				//$nb_err_suppr++;

				//echo "$sql<br />";
				//$suppr=mysql_query($sql);
				//if(!$suppr) {
				if($nb_err_suppr>0) {
					$msg="ERREUR lors de la suppression des copies associées au groupe n°$id_groupe.";
				}
				else {
					$sql="DELETE FROM eb_groupes WHERE id_epreuve='$id_epreuve' AND id_groupe='$id_groupe';";
					//echo "$sql<br />";
					$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$suppr) {
						$msg="ERREUR lors de la suppression du groupe n°$id_groupe.";
					}
					else {
						$msg="Suppression du groupe n°$id_groupe effectuée.";
					}
				}
			}
		}
		$mode='modif_epreuve';
	}
	elseif((isset($id_epreuve))&&($mode=='ajout_profs')) {
		check_token();

		// Ajout de groupes pour l'épreuve sélectionnée
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : (isset($_GET['login_prof']) ? $_GET['login_prof'] : array());

		$sql="DELETE FROM eb_profs WHERE id_epreuve='$id_epreuve';";
		$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$suppr) {
			$msg="ERREUR lors de la réinitialisation des professeurs inscrits.";
		}
		else {
			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg="L'épreuve n°$id_epreuve n'existe pas.<br />";
			}
			else {
				$tab_profs_inscrits=array();
				$msg="";
				for($i=0;$i<count($login_prof);$i++) {
					// On peut sélectionner plusieurs fois le même prof, mais il ne faut pas l'insérer plusieurs fois dans la table eb_profs
					if(!in_array($login_prof[$i],$tab_profs_inscrits)) {
						$tab_profs_inscrits[]=$login_prof[$i];
						$sql="INSERT INTO eb_profs SET id_epreuve='$id_epreuve', login_prof='$login_prof[$i]';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'ajout du professeur $login_prof[$i]<br />";
						}
					}
				}
				if(($msg=='')&&(count($login_prof)>0)) {$msg="Ajout de(s) professeur(s) effectué.";}

				// Vérification:
				// A-t-on supprimé un prof qui était associé à des copies?
				$sql="SELECT DISTINCT login_prof FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof!='';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					//$tab_profs_associes_copies=array();
					while($lig=mysqli_fetch_object($res)) {
						//$tab_profs_associes_copies
						if(!in_array($lig->login_prof,$tab_profs_inscrits)) {
							$sql="UPDATE eb_copies SET login_prof='' WHERE id_epreuve='$id_epreuve' AND login_prof='$lig->login_prof';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							$msg.="Suppression de professeur(s) qui étai(en)t associé(s) à des copies.<br />";
						}
					}
				}
			}
		}
		$mode='modif_epreuve';
	}
	elseif((isset($id_epreuve))&&($mode=='clore')) {
		check_token();

		// Cloture d'une épreuve
		$sql="UPDATE eb_epreuves SET etat='clos' WHERE id='$id_epreuve';";
		$cloture=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$cloture) {
			$msg="ERREUR lors de la cloture de l'épreuve $id_epreuve";
			unset($id_epreuve);
			unset($mode);
			break;
		}
		else {$msg="Cloture de l'épreuve n°$id_epreuve effectuée.";}
		unset($id_epreuve);
		unset($mode);
	}
	elseif((isset($id_epreuve))&&($mode=='declore')) {
		check_token();

		// Réouverture d'une épreuve
		$sql="UPDATE eb_epreuves SET etat='' WHERE id='$id_epreuve';";
		$cloture=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$cloture) {
			$msg="ERREUR lors de la réouverture de l'épreuve $id_epreuve";
			unset($id_epreuve);
			unset($mode);
			break;
		}
		else {
			$msg="Réouverture de l'épreuve n°$id_epreuve effectuée.";
			$mode='modif_epreuve';
		}
	}
	elseif((isset($id_epreuve))&&($mode=='copier_choix')&&(isset($_POST['copier_les_parametres']))) {
		check_token();

/*
    $_POST['id_groupe']=	Array (*)
    $_POST[id_groupe]['0']=	923
    $_POST[id_groupe]['1']=	934
    $_POST[id_groupe]['2']=	943
    $_POST[id_groupe]['3']=	952
    $_POST['id_salle']=	Array (*)
    $_POST[id_salle]['0']=	1
    $_POST[id_salle]['1']=	2
    $_POST[id_salle]['2']=	3
    $_POST[id_salle]['3']=	4
    $_POST['copie_affect_ele_salle']=	Array (*)
    $_POST[copie_affect_ele_salle]['0']=	1
    $_POST[copie_affect_ele_salle]['1']=	2
    $_POST[copie_affect_ele_salle]['2']=	3
    $_POST[copie_affect_ele_salle]['3']=	4
    $_POST['login_prof']=	Array (*)
    $_POST[login_prof]['0']=	BEAUNOIS
    $_POST[login_prof]['1']=	BOIREAUS
    $_POST['copie_affect_copie_prof']=	Array (*)
    $_POST[copie_affect_copie_prof]['0']=	BEAUNOIS
    $_POST[copie_affect_copie_prof]['1']=	BOIREAUS
    $_POST['id_epreuve']=	2
    $_POST['mode']=	copier_choix
    $_POST['copier_les_parametres']=	Copier les paramètres sélectionnés

    Nombre de valeurs en POST: 19

*/
		if(!isset($msg)) {$msg="";}

		$id_epreuve_modele=isset($_POST['id_epreuve_modele']) ? $_POST['id_epreuve_modele'] : NULL;

		$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : NULL;
		$id_salle=isset($_POST['id_salle']) ? $_POST['id_salle'] : NULL;
		$copie_affect_ele_salle=isset($_POST['copie_affect_ele_salle']) ? $_POST['copie_affect_ele_salle'] : NULL;
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;
		$copie_affect_copie_prof=isset($_POST['copie_affect_copie_prof']) ? $_POST['copie_affect_copie_prof'] : NULL;

		if(isset($id_groupe)) {

			//$sql="DELETE FROM eb_groupes WHERE id_epreuve='$id_epreuve';";
			//$del=mysql_query($sql);

			for($loop=0;$loop<count($id_groupe);$loop++) {
				$sql="SELECT 1=1 FROM eb_groupes WHERE id_groupe='$id_groupe[$loop]' AND  id_epreuve='$id_epreuve';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)==0) {
					$sql="INSERT INTO eb_groupes SET id_groupe='$id_groupe[$loop]', id_epreuve='$id_epreuve', transfert='n';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
		}

		// A REVOIR UN JOUR: La gestion des salles est mal foutue.
		// Il faudrait avoir une table eb_salles ne dépendant pas de id_epreuve
		if(isset($id_salle)) {
			$sql="DELETE FROM eb_salles WHERE id_epreuve='$id_epreuve';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="UPDATE eb_copies SET id_salle='' WHERE id_epreuve='$id_epreuve';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			$tab_corresp_id_salle=array();

			for($loop=0;$loop<count($id_salle);$loop++) {
				$sql="SELECT * FROM eb_salles WHERE id='$id_salle[$loop]' AND id_epreuve='$id_epreuve_modele';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$lig=mysqli_fetch_object($res);

					$sql="INSERT INTO eb_salles SET salle='$lig->salle', id_epreuve='$id_epreuve';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					$tmp_id_salle=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
					$tab_salle[$tmp_id_salle]=$lig->salle;
					$tab_corresp_id_salle[$id_salle[$loop]]=$tmp_id_salle;

					//echo "\$tab_salle[$tmp_id_salle]=".$tab_salle[$tmp_id_salle]."<br />";
					//echo "\$tab_corresp_id_salle[$id_salle[$loop]]=\$tab_corresp_id_salle[$id_salle[$loop]]=".$tab_corresp_id_salle[$id_salle[$loop]]."<br />";
				}
			}

			if(isset($copie_affect_ele_salle)) {
				for($loop=0;$loop<count($copie_affect_ele_salle);$loop++) {
					if(!in_array($copie_affect_ele_salle[$loop],$id_salle)) {
						$msg.="Il n'est pas possible de copier les affectations élèves/salles si la salle n'est pas copiée.<br />";
					}
					else {
						$sql="SELECT ec.login_ele FROM eb_copies ec WHERE ec.id_epreuve='$id_epreuve_modele' AND ec.id_salle='$copie_affect_ele_salle[$loop]';";
						//echo "$sql<br />";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							while($lig=mysqli_fetch_object($res)) {

								$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
								//echo "$sql<br />";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)>0) {
									$sql="UPDATE eb_copies SET id_salle='".$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]."' WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$update) {
										$msg.="Erreur lors de l'affectation de $lig->login_ele dans ".$tab_salle[$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]]."<br />";
									}
								}
								else {
									$sql="INSERT INTO eb_copies SET id_salle='".$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]."', id_epreuve='$id_epreuve', login_ele='".$lig->login_ele."', statut='v';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {
										$msg.="Erreur lors de l'affectation de $lig->login_ele dans ".$tab_salle[$tab_corresp_id_salle[$copie_affect_ele_salle[$loop]]]."<br />";
									}
								}

							}
						}
					}
				}
			}
		}

		// Si on n'a pas copié les groupes, on ne doit pas pouvoir copier les affectations... sauf si ce sont des profs qui ont les mêmes élèves dans plusieurs groupes.
		if(isset($login_prof)) {
			$sql="DELETE FROM eb_profs WHERE id_epreuve='$id_epreuve';";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="UPDATE eb_copies SET login_prof='' WHERE id_epreuve='$id_epreuve';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);

			for($loop=0;$loop<count($login_prof);$loop++) {
				$sql="INSERT INTO eb_profs SET login_prof='$login_prof[$loop]', id_epreuve='$id_epreuve';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}

			if(isset($copie_affect_copie_prof)) {
				for($loop=0;$loop<count($copie_affect_copie_prof);$loop++) {
					if(!in_array($copie_affect_copie_prof[$loop],$login_prof)) {
						$msg.="Il n'est pas possible de copier les affectations copies_élèves/correcteurs si le correcteur n'est pas copié.<br />";
					}
					else {
						$sql="SELECT ec.login_ele FROM eb_copies ec WHERE ec.id_epreuve='$id_epreuve_modele' AND ec.login_prof='$copie_affect_copie_prof[$loop]';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							while($lig=mysqli_fetch_object($res)) {

								$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)>0) {
									$sql="UPDATE eb_copies SET login_prof='".$copie_affect_copie_prof[$loop]."' WHERE id_epreuve='$id_epreuve' AND login_ele='$lig->login_ele';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$update) {
										$msg.="Erreur lors de l'affectation de la copie $lig->login_ele au correcteur ".$copie_affect_copie_prof[$loop]."<br />";
									}
								}
								else {
									$sql="INSERT INTO eb_copies SET login_prof='".$copie_affect_copie_prof[$loop]."', id_epreuve='$id_epreuve', login_ele='".$lig->login_ele."';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {
										$msg.="Erreur lors de l'affectation de la copie $lig->login_ele au correcteur ".$copie_affect_copie_prof[$loop]."<br />";
									}
								}

							}
						}
					}
				}
			}
		}

		$mode="modif_epreuve";
	}


	if($temoin_erreur_n_anonymat=='y') {
		if(!isset($msg)) {$msg="";}
		$msg.="<br />Une ou des erreurs se sont produites sur l'anonymat.<br />Vous devriez contrôler les numéros anonymat.";
	}
	elseif($temoin_n_anonymat=='y') {
		if(!isset($msg)) {$msg="";}
		$msg.="<br />Des numéros anonymat ont été modifiés. Regénérez si nécessaire les étiquettes/listes d'émargement.";
	}
}

/*
$truncate_tables=isset($_GET['truncate_tables']) ? $_GET['truncate_tables'] : NULL;
if($truncate_tables=='y') {
	$msg="<p>Nettoyage des tables Génèse des classes... <font color='red'>A FAIRE</font></p>\n";
	$sql="TRUNCATE TABLE ...;";
	//$del=mysql_query($sql);
}
*/

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Accueil";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "mode=$mode<br />";

//echo "<div class='noprint'>\n";
if((($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite'))&&(isset($mode))) {
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form_passage_a_une_autre_epreuve'>\n";
}
echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";
//echo "</p>\n";
//echo "</div>\n";

//include("../lib/calendrier/calendrier.class.php");


if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
	if(!isset($id_epreuve)) {

		//echo "<h2>Epreuve blanche</h2>\n";
		//echo "<blockquote>\n";

		if(!isset($mode)) {
			echo "</p>\n";

			echo "<ul>\n";
			// Créer une épreuve blanche
			echo "<li>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=creer_epreuve'>Créer une nouvelle épreuve</a></p>\n";
			echo "</li>\n";

			// Accéder aux épreuves blanches: closes ou non
			$sql="SELECT * FROM eb_epreuves WHERE etat!='clos' ORDER BY date, intitule;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				echo "<li>\n";
				echo "<p><b>Epreuves en cours&nbsp;:</b><br />\n";
				while($lig=mysqli_fetch_object($res)) {
					//echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;modif_epreuve=y'";
					echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=modif_epreuve'";
					if($lig->description!='') {
						echo " onmouseover=\"delais_afficher_div('div_epreuve_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_epreuve_".$lig->id."')\"";

						$titre="Epreuve n°$lig->id";
						$texte="<p><b>".$lig->intitule."</b><br />";
						$texte.=$lig->description;
						$tabdiv_infobulle[]=creer_div_infobulle('div_epreuve_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');

					}
					echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)";
					echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=suppr_epreuve".add_token_in_url()."' onclick=\"return confirm('Etes vous sûr de vouloir supprimer l épreuve?')\">Supprimer</a><br />\n";
				}
				echo "</li>\n";
			}
			// Pouvoir consulter/modifier:
			// - etat: clos ou non
			// - date
			// - intitule
			// - description
			// - liste des classes, groupes, profs
			// - Affecter les copies aux profs...

			$sql="SELECT * FROM eb_epreuves WHERE etat='clos' ORDER BY date, intitule;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				echo "<li>\n";
				echo "<p><b>Epreuves closes&nbsp;:</b><br />\n";
				while($lig=mysqli_fetch_object($res)) {
					echo "Epreuve $lig->intitule(<i>".formate_date($lig->date)."</i>)\n";

					echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=modif_epreuve'>Consulter</a>\n";

					echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;mode=declore".add_token_in_url()."' onclick=\"return confirm('Etes vous sûr de vouloir rouvrir l épreuve?')\">Rouvrir</a><br />\n";
				}

				//echo "<p style='color:red'>Permettre par la suite de rouvrir une épreuve close (pour correction).</p>\n";
				echo "</li>\n";
			}
			echo "</ul>\n";

			//echo "<p style='color:red'>A FAIRE ENCORE&nbsp;: Un lien pour vider toutes les tables d'épreuves blanches.<br />Est-ce qu'il faut vider ces tables lors de l'initialisation?<br />Si oui, peut-être ajouter une conservation dans les tables archivages (années antérieures).</p>\n";
			echo "<p><em>NOTES&nbsp;:</em> Au changement d'année, les tables 'eb_copies', 'eb_epreuves', 'eb_groupes' et 'eb_profs' sont vidées.</p>\n";
		}
		//===========================================================================
		// Création d'une épreuve
		elseif($mode=='creer_epreuve') {
			echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Menu épreuves blanches</a>\n";
			$sql="SELECT * FROM eb_epreuves ORDER BY date, intitule, description;";
			$res_eb=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_eb)>0) {
				echo " | <select name='id_epreuve' id='id_epreuve_a_passage_autre_epreuve' onchange=\"confirm_changement_epreuve(change, '$themessage');\">\n";
				echo "<option value=''>---</option>\n";
				while($lig_eb=mysqli_fetch_object($res_eb)) {
					echo "<option value='$lig_eb->id'>$lig_eb->intitule (".formate_date($lig_eb->date).")</option>\n";
				}
				echo "</select>\n";
				echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
				echo " <input type='submit' id='button_submit_passage_autre_epreuve' value='Go'>\n";

			}
			echo "</p>\n";
			echo "</form>\n";

			$indice_epreuve_courante=0;
			echo "<script type='text/javascript'>
	// Initialisation
	var change='no';

	document.getElementById('button_submit_passage_autre_epreuve').style.display='none';

	function confirm_changement_epreuve(thechange, themessage)
	{
		if(document.getElementById('id_epreuve_a_passage_autre_epreuve').selectedIndex!=0) {
			if (!(thechange)) thechange='no';
			if (thechange != 'yes') {
				document.forms['form_passage_a_une_autre_epreuve'].submit();
			}
			else{
				var is_confirmed = confirm(themessage);
				if(is_confirmed){
					document.forms['form_passage_a_une_autre_epreuve'].submit();
				}
				else{
					document.getElementById('id_epreuve_a_passage_autre_epreuve').selectedIndex=$indice_epreuve_courante;
				}
			}
		}
	}
</script>\n";


			echo "<p class='bold'>Création d'une épreuve blanche&nbsp;:</p>\n";

			echo "<blockquote>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			echo "<table summary='Paramètres'>\n";
			echo "<tr>\n";
			echo "<td>Intitule&nbsp;:</td>\n";
			echo "<td><input type='text' name='intitule' value='Epreuve blanche' onchange='changement()' /></td>\n";
			echo "</tr>\n";

			//$cal = new Calendrier("form1", "date");
		
			$annee=strftime("%Y");
			$mois=strftime("%m");
			$jour=strftime("%d");
			$date_defaut=$jour."/".$mois."/".$annee;

			$note_sur=20;

			echo "<tr>\n";
			echo "<td>Date de l'épreuve&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='date' value='$date_defaut' />\n";
			echo "<input type='text' name='date' id='date_epreuve' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date_plus_moins(this.id,event);\" />\n";
			//echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
			echo img_calendrier_js("date_epreuve", "img_bouton_date_epreuve");
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
			echo "<td>Note sur&nbsp;:</td>\n";
			echo "<td>\n";
			echo "<input type='text' name='note_sur' id='note_sur' value='$note_sur' size='3' onfocus=\"javascript:this.select()\" onkeydown=\"clavier_2(this.id,event,1,100);\" onchange=\"changement();\" autocomplete=\"off\" title=\"Vous pouvez modifier la valeur à l'aide des flèches Up et Down du pavé de direction.\" />\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td>Mode anonymat&nbsp;:</td>\n";
			echo "<td>\n";
			echo "<select name='type_anonymat' onchange='changement()'>\n";
			echo "<option value='elenoet'>Identifiant Elenoet</option>\n";
			echo "<option value='ele_id'>Identifiant Ele_id</option>\n";
			echo "<option value='no_gep'>Numéro INE</option>\n";
			echo "<option value='alea'>Chaine aléatoire</option>\n";
			echo "<option value='chrono'>Numéro Chronologique MC00nnn</option>\n";
			echo "</select>\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2' align='center'><input type='submit' name='creer_epreuve' value='Valider' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			//echo "<input type='hidden' name='is_posted' value='2' />\n";
			//echo "<p align='center'><input type='submit' name='creer_epreuve' value='Valider' /></p>\n";
			echo "</form>\n";
			echo "</blockquote>\n";

			echo "<p style='color:red'>NOTES&nbsp;:</p>";
			echo "<ul>";
			echo "<li><p style='color:red'>Le type_anonymat devrait être fixé une fois que l'on a contrôlé si l'ELENOET, l'INE sont renseignés pour les élèves choisis.</p></li>\n";
			echo "</ul>";
		}
	}
	//===========================================================================
	// Modification/compléments sur une épreuve
	elseif($mode=='modif_epreuve') {
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Menu épreuves blanches</a>\n";

		$indice_epreuve_courante=-1;
		$cpt_ep=0;
		$sql="SELECT * FROM eb_epreuves ORDER BY date, intitule, description;";
		$res_eb=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_eb)>0) {
			echo " | <select name='id_epreuve' id='id_epreuve_a_passage_autre_epreuve' onchange=\"confirm_changement_epreuve(change, '$themessage');\">\n";
			//echo "<option value=''>---</option>\n";
			while($lig_eb=mysqli_fetch_object($res_eb)) {
				echo "<option value='$lig_eb->id'";
				if($lig_eb->id==$id_epreuve) {
					echo " selected";
					$indice_epreuve_courante=$cpt_ep;
				}
				echo ">$lig_eb->intitule (".formate_date($lig_eb->date).")</option>\n";
				$cpt_ep++;
			}
			echo "</select>\n";
			echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			echo " <input type='submit' id='button_submit_passage_autre_epreuve' value='Go'>\n";

		}

		echo "<script type='text/javascript'>
	// Initialisation
	var change='no';

	document.getElementById('button_submit_passage_autre_epreuve').style.display='none';

	function confirm_changement_epreuve(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.forms['form_passage_a_une_autre_epreuve'].submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.forms['form_passage_a_une_autre_epreuve'].submit();
			}
			else{
				document.getElementById('id_epreuve_a_passage_autre_epreuve').selectedIndex=$indice_epreuve_courante;
			}
		}
	}
</script>\n";

		if(isset($id_epreuve)) {
		// VERIFIER: Il faut avoir saisi un intitulé,...
			$sql="SELECT 1=1 FROM eb_epreuves;";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=copier_choix".add_token_in_url()."'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">Copier des paramétrages depuis une autre épreuve blanche</a>\n";
			}
		}

		$aff=isset($_POST['aff']) ? $_POST['aff'] : (isset($_GET['aff']) ? $_GET['aff'] : NULL);
		if(!isset($aff)) {
			echo "</p>\n";
			echo "</form>\n";

			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'épreuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			$etat=$lig->etat;

			echo "<p><b>".(($etat!="clos") ? "Modification" : "Consultation")." d'une épreuve blanche&nbsp;:</b> Epreuve n°$id_epreuve</p>\n";

			//==============================================
			// Requêtes exploitées plus bas
			$sql="SELECT g.* FROM eb_groupes eg, groupes g WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name;";
			$res_groupes=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="SELECT u.* FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login ORDER BY u.nom,u.prenom;";
			//$sql="SELECT u.* FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login AND u.etat='actif' ORDER BY u.nom,u.prenom;";
			//echo "$sql<br />";
			$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($res_groupes)>0) {
				echo "<div style='float:right; width:15em; border: 1px solid black;' class='fieldset_opacite50'>\n";

				echo "<ol>\n";
				echo "<li>\n";
				echo "<a href='definir_salles.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				if($etat!='clos') {
					echo ">Définir les salles</a><br />\n";
				}
				else {
					echo ">Consulter les salles</a><br />\n";
				}
				// Proposer d'enregistrer des paramètres
				// Permettre d'organiser les élèves en salles
				// Générer CSV, PDF
				echo "</li>\n";
				echo "<li>\n";
				echo "<a href='genere_etiquettes.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">Générer les étiquettes</a><br />\n";
				// Proposer d'enregistrer des paramètres
				// Choisir les champs supplémentaires à afficher (date et lieu de naissance, INE, classe,...)
				// Permettre d'organiser les élèves en salles
				// Générer CSV, PDF
				echo "</li>\n";

				// Il n'est pas possible de générer lesfeuilles d'émargement sans affecter les élèves dans des salles
				$sql="SELECT * FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle!='-1';";
				$test_affect_salle=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_affect_salle)>0) {
					echo "<li>\n";
					echo "<a href='genere_emargement.php?id_epreuve=$id_epreuve'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Générer les feuilles d'émargement</a><br />\n";
					// Fiches avec:
					// - NOM_PRENOM;Champ_signature
					// - NOM_PRENOM;N_ANONYMAT;Champ_signature
					// Permettre d'organiser les élèves en salles
					// Générer CSV, PDF

					$sql="SELECT * FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle='-1';";
					$test_affect_salle=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_affect_salle)>0) {echo "<span style='color:red'>".mysqli_num_rows($test_affect_salle)." élève(s) non affecté(s) dans une salle.</span>";}
					echo "</li>\n";

					echo "<li>\n";
					echo "<a href='genere_liste_affichage.php?id_epreuve=$id_epreuve'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Générer les listes d'affichage</a><br />\n";
					// Proposer d'enregistrer des paramètres
					// Choisir les champs supplémentaires à afficher (date et lieu de naissance, INE, classe,...)
					// Permettre d'organiser les élèves en salles
					// Générer CSV, PDF
					echo "</li>\n";

				}

				if($etat!='clos') {
					if(mysqli_num_rows($res_profs)>0) {
						echo "<li>\n";
						echo "<a href='attribuer_copies.php?id_epreuve=$id_epreuve'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Attribuer les copies aux professeurs</a><br />\n";
						echo "</li>\n";
					}
					else {
						echo "<li>\n";
						echo "Aucun correcteur n'est encore choisi\n";
						echo "</li>\n";
					}
				}

				echo "<li>\n";
				echo "<a href='genere_bordereaux.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">Générer les bordereaux professeurs</a><br />\n";
				echo "</li>\n";

				echo "<li>\n";
				// A FAIRE
				//echo "<a href='.php?id_epreuve=$id_epreuve'";
				//echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				//echo ">&nbsp;</a>";
				echo "Passer l'épreuve en phase starting_block<br />\n";
				echo "</li>\n";

				echo "<li>\n";
				echo "<a href='saisie_notes.php?id_epreuve=$id_epreuve'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				if($etat!='clos') {
					echo ">Saisie des notes</a><br />\n";
				}
				else {
					echo ">Consulter les notes</a><br />\n";
				}
				echo "</li>\n";

				if($etat!='clos') {
					$info_affectation_salle="";
					$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle!='-1';";
					$test_affectation_salle=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_affectation_salle)==0) {
						$info_affectation_salle="<span style='color:red'>Aucun élève n'est encore affecté dans une salle.</span><br />\n";
					}

					//$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND statut='v' AND id_salle!='-1';";
					$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND statut='v';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(($info_affectation_salle=='')&&(mysqli_num_rows($test)==0)) {
						echo "<li>\n";
						echo "<a href='transfert_cn.php?id_epreuve=$id_epreuve'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Transfert vers carnets de notes</a><br />\n";
						echo "</li>\n";

						echo "<li>\n";
						echo "<a href='bilan.php?id_epreuve=$id_epreuve'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Bilan de l'épreuve</a><br />\n";
						echo "</li>\n";

						echo "<li>\n";
						echo "<a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=clore".add_token_in_url()."'";
						echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
						echo ">Clore l'épreuve</a><br />\n";
						echo "</li>\n";
					}
					else {
						echo "<li>\n";
						echo $info_affectation_salle;
						echo mysqli_num_rows($test)." note(s) non encore saisie(s).<br />\n";
						echo "Les choix suivants ne sont donc pas encore accessibles&nbsp;:";
						echo "<ul>\n";
						echo "<li>Transfert vers carnets de notes</li>\n";
						echo "<li>Bilan de l'épreuve</li>\n";
						echo "<li>Clore l'épreuve</li>\n";
						echo "</ul>\n";
					}
				}
				else {
					echo "<li>\n";
					echo "<a href='bilan.php?id_epreuve=$id_epreuve'";
					echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
					echo ">Bilan de l'épreuve</a><br />\n";
					echo "</li>\n";
				}
				echo "</ol>\n";

				echo "</div>\n";

				/*
				echo "<div style='float:right; width:15em; border: 1px solid black;'>\n";
				echo "Proposer de passer l'épreuve en phase starting_block";
				echo "</div>\n";
				*/
			}
			//==============================================

			echo "<blockquote>\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			echo "<table summary='Paramètres'>\n";
			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Intitule&nbsp;:</td>\n";
			if($etat!='clos') {
				echo "<td><input type='text' name='intitule' value='$lig->intitule' size='34' onchange='changement()' /></td>\n";
			}
			else {
				echo "<td>$lig->intitule</td>\n";
			}
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
	
			$note_sur=$lig->note_sur;

			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Date de l'épreuve&nbsp;:</td>\n";
			echo "<td>\n";

			if($etat!='clos') {
				echo "<input type='text' name='date' id='date_epreuve' value='$date_defaut' size='10' onchange='changement()' onKeyDown=\"clavier_date(this.id,event);\" />\n";
				//echo "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
				echo img_calendrier_js("date_epreuve", "img_bouton_date_epreuve");
			}
			else {
				echo $date_defaut;
			}

			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td style='font-weight:bold; vertical-align:top;'>Description&nbsp;:</td>\n";
			echo "<td>\n";
			//echo "<input type='text' name='description' value='' />";
			if($etat!='clos') {
				echo "<textarea class='wrap' name=\"no_anti_inject_description\" rows='4' cols='40' onchange='changement()'>".$lig->description."</textarea>\n";
			}
			else {
				echo nl2br($lig->description);
			}
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td style='font-weight:bold; vertical-align:top;'>Note sur&nbsp;:</td>\n";
			echo "<td>\n";
			if($etat!='clos') {
				echo "<input type='text' name='note_sur' id='note_sur' value='$note_sur' size='3' onfocus=\"javascript:this.select()\" onkeydown=\"clavier_2(this.id,event,1,100);\" onchange=\"changement();\" autocomplete=\"off\" title=\"Vous pouvez modifier la valeur à l'aide des flèches Up et Down du pavé de direction.\" />\n";
			}
			else {
				echo $note_sur;
			}
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td style='font-weight:bold;'>Mode anonymat&nbsp;:</td>\n";
			echo "<td>\n";
			if($etat!='clos') {
				echo "<select name='type_anonymat' onchange='changement()'>\n";
		
				echo "<option value='elenoet'";
				if($lig->type_anonymat=='elenoet') {echo " selected='true'";}
				echo ">Identifiant Elenoet</option>\n";
		
				echo "<option value='ele_id'";
				if($lig->type_anonymat=='ele_id') {echo " selected='true'";}
				echo ">Identifiant Ele_id</option>\n";
		
				echo "<option value='no_gep'";
				if($lig->type_anonymat=='no_gep') {echo " selected='true'";}
				echo ">Numéro INE</option>\n";
		
				echo "<option value='alea'";
				if($lig->type_anonymat=='alea') {echo " selected='true'";}
				echo ">Chaine aléatoire</option>\n";
		
				echo "<option value='chrono'";
				if($lig->type_anonymat=='chrono') {echo " selected='true'";}
				echo ">Numéro Chronologique MC00nnn</option>\n";
		
				echo "</select>\n";
			}
			elseif($lig->type_anonymat=='no_gep') {
				echo "Numéro INE";
			}
			else {
				echo "Identifiant ".strtoupper($lig->type_anonymat);
			}
			echo "</td>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<td colspan='2' align='center'>";
			if($etat!='clos') {echo "<input type='submit' name='modif_epreuve' value='Valider' />";} else {echo "&nbsp;";}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
			echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			//echo "<input type='hidden' name='is_posted' value='2' />\n";
			//echo "<p align='center'><input type='submit' name='modif_epreuve' value='Valider' /></p>\n";
			echo "</form>\n";
			echo "</blockquote>\n";

			/*
			echo "<p class='bold'>Compléter les éléments de l'épreuve&nbsp;:</p>\n";
			echo "<ul>\n";
			echo "<li><a href=''></a></li>\n";
			echo "</ul>\n";

			echo "<p><b>Compléter les éléments de l'épreuve&nbsp;:</b> <a href=''></a></p>\n";
			*/

			//=====================================

			//$sql="SELECT g.* FROM eb_groupes eg, groupes g WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name;";
			//$res_groupes=mysql_query($sql);
			if(mysqli_num_rows($res_groupes)>0) {
				echo "<p><b>Liste des groupes inscrits à l'épreuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";
				while($lig=mysqli_fetch_object($res_groupes)) {

					//$current_group=get_group($lig->id);

					$sql="SELECT DISTINCT c.classe FROM classes c, j_groupes_classes jgc WHERE jgc.id_groupe='".$lig->id."' AND jgc.id_classe=c.id ORDER BY classe;";
					$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
					$cpt=0;
					$classlist_string="";
					while($lig_class=mysqli_fetch_object($res_classes)) {
						if($cpt>0) {$classlist_string.=", ";}
						$classlist_string.=$lig_class->classe;
						$cpt++;
					}

					//echo "<b>".$current_group['classlist_string']."</b> <a href='#'>".htmlspecialchars($lig->name)."</a> (<i>".htmlspecialchars($lig->description)."</i>)";
					//echo "<b>".$current_group['classlist_string']."</b> ".htmlspecialchars($lig->name)." (<i>".htmlspecialchars($lig->description)."</i>)";
					echo "<b>".$classlist_string."</b> ".htmlspecialchars($lig->name)." (<i>".htmlspecialchars($lig->description)."</i>)";
					if($etat!='clos') {
						echo " - <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;id_groupe=$lig->id&amp;mode=suppr_groupe".add_token_in_url()."' onclick=\"return confirm('Etes vous sûr de vouloir supprimer le groupe de l épreuve?')\">Supprimer</a>\n";
					}
					echo "<br />\n";
					// Afficher les élèves inscrits/non inscrits en infobulle
					// Permettre de cocher/décocher les élèves dans ces infobulles
				}
			}
			else {
				echo "<p><b>Aucun groupe n'est encore inscrit à l'épreuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";
			}
			if($etat!='clos') {
				echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=classes'>Ajouter des groupes</a></p>\n";
			}
			echo "</blockquote>\n";

			//=====================================

			// Effectuer un contrôle de l'anonymat:
			// Nombre d'inscrits et nombre de numéros distincts
			// Pouvoir modifier le type anonymat

			//=====================================

			//$sql="SELECT u.* FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login ORDER BY u.nom,u.prenom;";
			//$res_profs=mysql_query($sql);
			if(mysqli_num_rows($res_profs)>0) {
				echo "<p><b>Liste des professeurs heureux correcteurs désignés pour l'épreuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";
				while($lig=mysqli_fetch_object($res_profs)) {
					//echo "<a href='#'>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1)."<br />\n";
					echo $lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1);
					//echo " <span style='color:red'>Compter les copies attribuées</span>";

					$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof='".$lig->login."';";
					$compte_total=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));

					if($compte_total==0) {
						echo " (<span style='font-style:italic;color:red;'>aucune copie attribuée</span>)";
					}
					else {
						$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND login_prof='".$lig->login."' AND statut!='v';";
						$compte_saisie=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));
						echo " (<span style='font-style:italic;";
						if($compte_saisie==$compte_total) {
							echo "color:green;";
						}
						else {
							echo "color:red;";
						}
						echo "'>$compte_saisie/$compte_total</span>)";
					}
					echo "<br />\n";
				}

				if($etat!='clos') {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=profs'>Ajouter/supprimer des professeurs correcteurs</a></p>\n";
				}
			}
			else {
				echo "<p><b>Aucun correcteur n'est encore désigné pour l'épreuve&nbsp;:</b></p>\n";
				echo "<blockquote>\n";

				if($etat!='clos') {
					echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=profs'>Ajouter des professeurs correcteurs</a></p>\n";
				}
			}
			//echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=profs'>Ajouter des professeurs correcteurs</a></p>\n";
			echo "</blockquote>\n";

			//=====================================

			if(mysqli_num_rows($res_groupes)>0) {
				$sql="SELECT * FROM eb_salles es WHERE es.id_epreuve='$id_epreuve' ORDER BY es.salle;";
				$res_salles=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_salles)>0) {
					echo "<p><b>Liste des salles choisies pour l'épreuve&nbsp;:</b></p>\n";
					echo "<blockquote>\n";
					while($lig=mysqli_fetch_object($res_salles)) {
						$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$id_epreuve' AND id_salle='$lig->id';";
						$res_eff=mysqli_query($GLOBALS["mysqli"], $sql);
						echo "Salle '<b>$lig->salle</b>' (<i>eff.".mysqli_num_rows($res_eff)."</i>)";
						echo "<br />\n";
					}
					echo "</blockquote>\n";
				}
				else {
					echo "<p><b>Aucun salle n'est encore choisie pour l'épreuve.</b></p>\n";
				}
			}
			echo "<p><br /></p>";

			echo "<p style='color:red'>NOTES&nbsp;:</p>";
			echo "<ul>";
			echo "<li><p style='color:red'>A FAIRE: Pouvoir interdire (ou alerter) la modification du type_anonymat une fois les listings_émargement/etiquettes générés.<br />Mettre par exemple eb_epreuves.etat='starting_block' et interdire alors les modifs d'anonymat.</p></li>\n";
			//echo "<li><p style='color:red'>Ajouter des changement() et confirm_abandon() quand on quitte la page sans valider une modif.</p></li>\n";
			//echo "<li><p style='color:red'>A FAIRE: afficher la liste des salles associées et les effectifs dans les salles.</p></li>\n";
			echo "</ul>";

		}
		//=============================================================================
		elseif($aff=='classes') {
			// Choix des classes

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve' onclick=\"return confirm_abandon (this, change, '$themessage')\">Epreuve $id_epreuve</a>\n";
			echo "</p>\n";
			echo "</form>\n";

			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'épreuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			$etat=$lig->etat;

			if($etat=='clos') {
				echo "<p class='bold'>L'épreuve $id_epreuve est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p class='bold'>Choix des classes pour l'épreuve $id_epreuve&nbsp;:</p>\n";

			if($_SESSION['statut']=='administrateur') {
				$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe";
			}
			elseif($_SESSION['statut']=='scolarite') {
				$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes j WHERE p.id_classe = c.id AND j.id_classe=c.id ORDER BY classe";
				// Permettre aussi de voir toutes les classes...
			}
			$classes_list = mysqli_query($GLOBALS["mysqli"], $sql);
			$nb = mysqli_num_rows($classes_list);
			if ($nb==0) {
				echo "<p>Aucune classe ne semble définie.</p>\n";
			}
			else {
				// Liste des classes déjà associées à l'épreuve via des groupes inscrits dans eb_groupes
				$tab_id_classe=array();
				$sql="SELECT DISTINCT j.id_classe FROM eb_groupes eg, j_groupes_classes j WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=j.id_groupe";
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
	
				/*
				while ($i < $nb) {
					$id_classe = old_mysql_result($classes_list, $i, 'id');
					$temp = "case_".$id_classe;
					$classe = old_mysql_result($classes_list, $i, 'classe');
	
					if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					echo "<label for='$temp' style='cursor: pointer;'>";
					echo "<input type='checkbox' name='id_classe[]' id='$temp' value='$id_classe' ";
					if(in_array($id_classe,$tab_id_classe)) {echo "checked ";}
					echo "/>";
					echo "Classe : $classe</label><br />\n";
					$i++;
				}
				*/
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
					echo "onchange=\"checkbox_change($i); changement();\" ";
					if(in_array($id_classe,$tab_id_classe)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='id_classe_$i'><span id='texte_id_classe_$i'$temp_style>Classe : ".$classe.".</span></label><br />\n";
					$i++;
				}

				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				//echo "<input type='hidden' name='is_posted' value='2' />\n";

				echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
				echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
				echo "<input type='hidden' name='aff' value='groupes' />\n";
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
		elseif($aff=='groupes') {
			//Choix des groupes

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve' onclick=\"return confirm_abandon (this, change, '$themessage')\">Epreuve $id_epreuve</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=classes' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choix des classes</a>\n";
			echo "</p>\n";
			echo "</form>\n";


			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'épreuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			$etat=$lig->etat;

			if($etat=='clos') {
				echo "<p class='bold'>L'épreuve $id_epreuve est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}


			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

			if(!isset($id_classe)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'a été choisie.</p>\n";
			}

			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			echo "<p class='bold'>Choix des groupes pour l'épreuve $id_epreuve&nbsp;:</p>\n";

			$sql="SELECT type_anonymat FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			$lig=mysqli_fetch_object($res);
			if($lig->type_anonymat=='alea') {
				echo "<div style='float:right; width:20em; border: 1px solid black; margin:2px;' class='fieldset_opacite50'>\n";
				echo "<p>Le type d'anonymat choisi est un numéro 'aléatoire'.</p>\n";
				echo "<p><b>Attention</b>&nbsp;: Lors de la validation de ce formulaire, les numéros d'anonymat sont générés/regénérés.<br />Vous ne devriez pas valider ce formulaire une fois que des étiquettes ont été collées ou des copies anonymées.</p>\n";
				echo "</div>\n";
			}
			// Ajout Eric Mode chrono
			if($lig->type_anonymat=='chrono') {
				echo "<div style='float:right; width:20em; border: 1px solid black;'>\n";
				echo "<p>Le type d'anonymat choisi est un numéro Chronologique.</p>\n";
				echo "<p><b>Attention</b>&nbsp;: Lors de la validation de ce formulaire, les numéros d'anonymat sont générés/regénérés.<br />Vous ne devriez pas valider ce formulaire une fois que des étiquettes ont été collées ou des copies anonymées.</p>\n";
				echo "</div>\n";
			}


			$tab_groupes_inscrits=array();
			$sql="SELECT id_groupe FROM eb_groupes eg WHERE eg.id_epreuve='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					$tab_groupes_inscrits[]=$lig->id_groupe;
				}
			}

			echo "<table class='boireaus' summary='Groupes triés par classes'>\n";
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
				echo "<td class='lig$alt' style='text-align:left;vertical-align:top;'>\n";
				$sql="SELECT g.* FROM groupes g, j_groupes_classes jgc WHERE jgc.id_groupe=g.id AND jgc.id_classe='$id_classe[$i]' ORDER BY g.name;";
				//echo "$sql<br />\n";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_$cpt' value='$lig->id' ";
						echo "onchange=\"checkbox_change($cpt); changement()\" ";
						if(in_array($lig->id,$tab_groupes_inscrits)) {echo "checked ";$temp_style="style='font-weight:bold;'";} else {$temp_style="";}
						echo "/><label for='id_groupe_$cpt' style='cursor: pointer;'><span id='texte_id_groupe_$cpt' $temp_style>".htmlspecialchars($lig->name)." (<span style='font-style:italic;font-size:x-small;'>".htmlspecialchars($lig->description)."</span>)</span></label><br />\n";
						$cpt++;
					}
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
			//echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			echo "<input type='hidden' name='mode' value='ajout_groupes' />\n";
			//echo "<input type='hidden' name='aff' value='groupes' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<p><br /></p>&nbsp;\n";

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
</script>\n";

		}
		/*
		elseif($aff=='eleves') {
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve'>Epreuve $id_epreuve</a>\n";
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve&amp;aff=classes'>Choix des classes</a>\n";
			echo "</p>\n";

			$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;

			if(!isset($id_classe)) {
				echo "<p style='color:red'>ERREUR&nbsp;: Aucune classe n'a été choisie.</p>\n";
			}

			for($i=0;$i<count($id_classe);$i++) {


			}

		}
		*/
		//=============================================================================
		elseif($aff=='profs') {
			// Choix des profs

			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve' onclick=\"return confirm_abandon (this, change, '$themessage')\">Epreuve $id_epreuve</a>\n";
			echo "</p>\n";
			echo "</form>\n";


			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red;'>ERREUR&nbsp;: L'épreuve $id_epreuve n'existe pas.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
			$lig=mysqli_fetch_object($res);
			$etat=$lig->etat;

			if($etat=='clos') {
				echo "<p class='bold'>L'épreuve $id_epreuve est close.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}


			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			$tab_profs_deja_punis=array();
			$sql="SELECT u.login FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login ORDER BY u.nom,u.prenom;";
			//$sql="SELECT u.login FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND ep.login_prof=u.login AND u.etat='actif' ORDER BY u.nom,u.prenom;";
			$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_profs)>0) {
				while($lig=mysqli_fetch_object($res_profs)) {
					$tab_profs_deja_punis[]=$lig->login;
				}
			}

			$cpt=0;
			$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM eb_groupes eg, j_groupes_professeurs jgp, utilisateurs u WHERE eg.id_epreuve='$id_epreuve' AND eg.id_groupe=jgp.id_groupe AND u.login=jgp.login ORDER BY u.nom,u.prenom;";
			$res_profs_groupes=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_profs_groupes)>0) {
				echo "<p>Les professeurs appelés à corriger sont probablement les enseignants des groupes sélectionnés.</p>\n";
				//$cpt=0;
				while($lig=mysqli_fetch_object($res_profs_groupes)) {
					echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
					echo "onchange=\"checkbox_change($cpt);changement();\" ";
					//if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";}
					//echo "/><label for='login_prof_$cpt'>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1).".</span></label><br />\n";
					if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1).".</span></label><br />\n";
					$cpt++;
				}
			}

			//$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM utilisateurs u WHERE u.statut='professeur' ORDER BY u.nom,u.prenom;";
			$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM utilisateurs u WHERE u.statut='professeur' AND u.etat='actif' ORDER BY u.nom,u.prenom;";
			$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_profs)>0) {
				echo "<p>Sélectionner des professeurs sans préoccupation de groupes&nbsp;:</p>\n";

				$nb=mysqli_num_rows($res_profs);
				$nb_prof_par_colonne=round($nb/3);
				echo "<table width='100%' summary='Choix des professeurs'>\n";
				echo "<tr valign='top' align='center'>\n";
	
				$i=0;
				echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
				echo "<td align='left'>\n";
	
				while ($i < $nb) {

					$lig=mysqli_fetch_object($res_profs);

					if(($i>0)&&(round($i/$nb_prof_par_colonne)==$i/$nb_prof_par_colonne)){
						echo "</td>\n";
						//echo "<td style='padding: 0 10px 0 10px'>\n";
						echo "<td align='left'>\n";
					}
	
					echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
					echo "onchange=\"checkbox_change($cpt);changement();\" ";
					if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
					echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1).".</span></label><br />\n";
					$cpt++;

					$i++;
				}
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";

				/*
				while($lig=mysql_fetch_object($res_profs)) {
					echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
					if(in_array($lig->login,$tab_profs_deja_punis)) {echo "checked ";}
					echo "/><label for='login_prof_$cpt'>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1).".</label><br />\n";
					$cpt++;
				}
				*/
			}

			echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
			//echo "<input type='hidden' name='mode' value='modif_epreuve' />\n";
			echo "<input type='hidden' name='mode' value='ajout_profs' />\n";
			echo "<p align='center'><input type='submit' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('login_prof_'+cpt)) {
		if(document.getElementById('login_prof_'+cpt).checked) {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

		}
	}


	//===========================================================================
	// Copie de paramétrages
	elseif($mode=='copier_choix') {
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Menu épreuves blanches</a>\n";
		echo " | <a href='".$_SERVER['PHP_SELF']."?mode=modif_epreuve&amp;id_epreuve=$id_epreuve' onclick=\"return confirm_abandon (this, change, '$themessage')\">Epreuve blanche n°$id_epreuve</a>\n";

// Classes
// Salles
	// Affectation des élèves dans les salles
// Matières
	// Profs
// Affectation des copies aux profs
// Ou rotation de l'affectation des copies aux profs
/*
eb_copies
eb_epreuves
eb_groupes
eb_profs
eb_salles
*/

		$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "</p>\n";
			echo "</form>\n";

			echo "<p style='color:red'>L'épreuve n°$id_epreuve n'existe pas.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else {
			$lig=mysqli_fetch_object($res);
			$intitule_epreuve=$lig->intitule;
			$description_epreuve=$lig->description;
			$date_epreuve=$lig->date;
		}

		if(!isset($id_epreuve_modele)) {
			echo "</p>\n";
			echo "</form>\n";

			echo "<p>Choix d'un modèle pour l'épreuve n°$id_epreuve</p>\n";

			echo "<p class='bold'>Liste des autres épreuves&nbsp;:</p>\n";
			$sql="SELECT * FROM eb_epreuves WHERE id!='$id_epreuve';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red'>Aucune autre épreuve n'a été trouvée.</p>\n";
			}
			else {
				echo "<ul>\n";
				while($lig=mysqli_fetch_object($res)) {
					echo "<li><p><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;mode=copier_choix&amp;id_epreuve_modele=$lig->id".add_token_in_url()."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Epreuve n°$lig->id</a>&nbsp;: $lig->intitule<br />".nl2br($lig->description)."</p></li>\n";
				}
				echo "</ul>\n";
			}
		}
		else {
			echo " | <a href='".$_SERVER['PHP_SELF']."?mode=copier_choix&amp;id_epreuve=$id_epreuve' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choix de l'épreuve modèle</a>\n";
			echo "</p>\n";
			echo "</form>\n";

			echo "<h3>Copie de paramètres pour l'épreuve n°$id_epreuve: $intitule_epreuve (".formate_date($date_epreuve).")</h3>\n";

			$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve_modele';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<p style='color:red'>L'épreuve n°$id_epreuve_modele n'a pas été trouvée.</p>\n";
			}
			else {
				$lig=mysqli_fetch_object($res);


				echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form_copie_param_modele'>\n";
				echo add_token_field();

				//echo "<p>Modèle&nbsp;: Epreuve n°$lig->id&nbsp;: $lig->intitule<br />".nl2br($lig->description)."</p>";
				//echo "<div style='width:11em; font-weight:bold; display:inline; border:1px solid black;'>Modèle&nbsp;:</div><div style='display:block; border:1px solid black; width:30em'>Epreuve n°$lig->id&nbsp;: $lig->intitule<br />".nl2br($lig->description)."</div>";
				echo "<p><b>Modèle&nbsp;:</b> Epreuve n°$lig->id&nbsp;:</p>\n";
				echo "<p style='margin-left: 3em;'>$lig->intitule<br />".nl2br($lig->description)."</p>";

				// Liste des groupes
				$sql="SELECT * FROM eb_groupes eg WHERE id_epreuve='$id_epreuve_modele';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					echo "<p>L'épreuve n°$id_epreuve_modele n'est associée à aucun groupe/enseignement.</p>\n";
				}
				else {
					echo "<p>Liste des enseignements de l'épreuve modèle&nbsp;:</p>\n";
					echo "<p style='margin-left: 3em;'>";
					while($lig=mysqli_fetch_object($res)) {
						$tmp_grp=get_group($lig->id_groupe);
						echo "<input type='checkbox' name='id_groupe[]' id='id_groupe_".$lig->id_groupe."' value='$lig->id_groupe' onchange='changement()' checked /><label for='id_groupe_".$lig->id_groupe."'> ".$tmp_grp['name']." (<i>".$tmp_grp['classlist_string']."</i>)</label>\n";
					}
					echo "</p>\n";
				}

				// Liste des salles
				$sql="SELECT * FROM eb_salles es WHERE id_epreuve='$id_epreuve_modele' ORDER BY salle;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					echo "<p>L'épreuve n°$id_epreuve_modele n'est associée à aucune salle.</p>\n";
				}
				else {
					echo "<p>Liste des salles&nbsp;:</p>\n";
					echo "<p style='margin-left: 3em;'>";
					while($lig=mysqli_fetch_object($res)) {
						echo "<input type='checkbox' name='id_salle[]' id='id_salle_".$lig->id."' value='$lig->id' onchange='changement()' checked /><label for='id_salle_".$lig->id."'> ".$lig->salle."</label>\n";
						$tab_salle[$lig->id]=$lig->salle;
					}
					echo "</p>\n";

					// Liste des associations élèves/salles
					$sql="SELECT * FROM eb_copies WHERE id_epreuve='$id_epreuve_modele' ORDER BY id_salle;";
					$res_ele_salle=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ele_salle)==0) {
						echo "<p>Aucun élève n'est affecté dans une salle pour l'épreuve n°$id_epreuve_modele.</p>\n";
					}
					else {
						$current_id_salle="";
						$cpt=0;
						while($lig=mysqli_fetch_object($res_ele_salle)) {

							if($lig->id_salle!='-1') {

								if($lig->id_salle!=$current_id_salle) {
									//if($current_id_salle!="";) {echo "</table>\n";}
									if($current_id_salle!="") {
										echo "</span>\n";
										echo "</span>\n";
										echo "</p>\n";
									}
	
									$current_id_salle=$lig->id_salle;
									echo "<p style='margin-left: 6em;'>";
									echo "<span class='conteneur_infobulle_css'>\n";
									echo "<input type='checkbox' name='copie_affect_ele_salle[]' id='copie_affect_ele_salle_".$lig->id_salle."' value='".$lig->id_salle."' onchange='changement()' checked  />";
									echo "<label for='copie_affect_ele_salle_".$lig->id_salle."'>";
									//if($lig->id_salle!='-1') {
										echo "Copier les affectations d'élèves en ".$tab_salle[$lig->id_salle]."</label>";
									/*
									}
									else {
										echo "Copier la liste des élèves affectés dans aucune salle</label>";
									}
									*/
									//echo "<table class='boireaus' summary=\"Liste des élèves affectés en Salle $tab_salle[$lig->id]\">\n";
									echo "<br />\n";
									echo "<span class='infobulle_css'>\n";
									$cpt=0;
								}
	
								//echo "<input type='checkbox' name='' value='' />";
								if($cpt>0) {
									echo ", ";
									if($cpt%5==0) {echo "<br />";}
								}
								echo get_nom_prenom_eleve($lig->login_ele,'avec_classe');
								$cpt++;
							}
						}
						//echo "</table>\n";
						echo "</span>\n";
						echo "</span>\n";
						echo "</p>\n";

					}
				}

				// Liste des correcteurs
				$sql="SELECT * FROM eb_profs ep WHERE id_epreuve='$id_epreuve_modele' ORDER BY login_prof;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					echo "<p>L'épreuve n°$id_epreuve_modele n'est associée à aucun correcteur.</p>\n";
				}
				else {
					echo "<p>Liste des correcteurs&nbsp;:</p>\n";
					echo "<p style='margin-left: 3em;'>";
					while($lig=mysqli_fetch_object($res)) {
						$tab_prof[$lig->login_prof]=civ_nom_prenom($lig->login_prof);

						echo "<input type='checkbox' name='login_prof[]' id='login_prof_".$lig->login_prof."' value='$lig->login_prof' onchange='changement()' checked /><label for='login_prof_".$lig->login_prof."'> ".$tab_prof[$lig->login_prof]."</label>\n";
					}
					echo "</p>\n";

					// Liste des associations professeur/copies
					$sql="SELECT * FROM eb_copies WHERE id_epreuve='$id_epreuve_modele' AND login_prof!='' ORDER BY login_prof;";
					//echo "$sql<br />";
					$res_ele_prof=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ele_prof)==0) {
						echo "<p>Aucun copie élève n'est affectée à un correcteur pour l'épreuve n°$id_epreuve_modele.</p>\n";
					}
					else {
						$current_login_prof="";
						$cpt=0;
						while($lig=mysqli_fetch_object($res_ele_prof)) {

							if($lig->login_prof!=$current_login_prof) {
								if($current_login_prof!="") {
									echo "</span>\n";
									echo "</span>\n";
									echo "</p>\n";
								}
								$current_login_prof=$lig->login_prof;

								echo "<p style='margin-left: 6em;'>";
								echo "<span class='conteneur_infobulle_css'>\n";
								echo "<input type='checkbox' name='copie_affect_copie_prof[]' id='copie_affect_copie_prof_".$lig->login_prof."' value='".$lig->login_prof."' onchange='changement()' checked  />";
								echo "<label for='copie_affect_copie_prof_".$lig->login_prof."'>Copier les affectations copie d'élèves au correcteur ".$tab_prof[$lig->login_prof]."</label>";
								//echo "<table class='boireaus' summary=\"Liste des élèves affectés en Salle $tab_salle[$lig->id]\">\n";
								echo "<br />\n";
								echo "<span class='infobulle_css'>\n";
								$cpt=0;
							}

							//echo "<input type='checkbox' name='' value='' />";
							if($cpt>0) {
								echo ", ";
								if($cpt%5==0) {echo "<br />";}
							}
							echo get_nom_prenom_eleve($lig->login_ele,'avec_classe');
							$cpt++;
						}
						//echo "</table>\n";
						echo "</span>\n";
						echo "</span>\n";
						echo "</p>\n";

					}
				}

				echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
				echo "<input type='hidden' name='id_epreuve_modele' value='$id_epreuve_modele' />\n";
				echo "<input type='hidden' name='mode' value='copier_choix' />\n";
				echo "<input type='submit' name='copier_les_parametres' value='Copier les paramètres sélectionnés' />\n";
				echo "</form>\n";

				// Espace pour que les infobulles CSS puissent s'afficher.
				echo "<div style='height:10em;'>&nbsp;</div>";



			}
		}


	}





}
//=============================================================================
elseif($_SESSION['statut']=='professeur') {
	// Menu professeur

	// Accéder aux épreuves blanches qui lui sont affectées
	$sql="SELECT ee.* FROM eb_epreuves ee, eb_profs ep WHERE ep.login_prof='".$_SESSION['login']."' AND ee.id=ep.id_epreuve AND ee.etat!='clos' ORDER BY ee.date, ee.intitule;";
	//echo "$sql<br />\n";

	// Afficher les épreuves auxquelles est associé le prof
	// Pointer vers des pages:
	// - saisie
	// - génération d'un listing n_anonymat,note,statut

	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p><b>Epreuves en cours&nbsp;:</b><br />\n";
		echo "<ul>\n";
		while($lig=mysqli_fetch_object($res)) {
			echo "<li>\n";

			$sql="SELECT 1=1 FROM eb_copies WHERE id_epreuve='$lig->id';";
			$test1=mysqli_query($GLOBALS["mysqli"], $sql);
			
			$sql="SELECT DISTINCT n_anonymat FROM eb_copies WHERE id_epreuve='$lig->id';";
			$test2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test1)!=mysqli_num_rows($test2)) {
				echo "<span style='color:red;'>Les numéros anonymats ne sont pas uniques sur l'épreuve (<i>cela ne devrait pas arriver</i>).<br />La saisie n'est pas possible sur l'épreuve </span>";
				if($lig->description!='') {
					echo "<a href='#'";
					echo " onmouseover=\"delais_afficher_div('div_epreuve_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_epreuve_".$lig->id."')\"";
	
					$titre="Epreuve n°$lig->id";
					$texte="<p><b>".$lig->intitule."</b><br />";
					$texte.=$lig->description;
					$tabdiv_infobulle[]=creer_div_infobulle('div_epreuve_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');
	
					echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)<br />\n";
				}
				else {
					echo "$lig->intitule (<i>".formate_date($lig->date)."</i>)<br />\n";
				}
			}
			else {
				//echo "Modifier <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$lig->id&amp;modif_epreuve=y'";
				echo "Saisir les notes pour <a href='saisie_notes.php?id_epreuve=$lig->id'";
				if($lig->description!='') {
					echo " onmouseover=\"delais_afficher_div('div_epreuve_".$lig->id."','y',-100,20,1000,20,20)\" onmouseout=\"cacher_div('div_epreuve_".$lig->id."')\"";
	
					$titre="Epreuve n°$lig->id";
					$texte="<p><b>".$lig->intitule."</b><br />";
					$texte.=$lig->description;
					$tabdiv_infobulle[]=creer_div_infobulle('div_epreuve_'.$lig->id,$titre,"",$texte,"",30,0,'y','y','n','n');
	
				}
				echo ">$lig->intitule</a> (<i>".formate_date($lig->date)."</i>)<br />\n";
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	else {
		echo "<p>Aucune épreuve n'est ouverte.</p>";
	}
}

require("../lib/footer.inc.php");
?>
