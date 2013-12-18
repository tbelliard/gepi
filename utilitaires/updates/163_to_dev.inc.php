<?php
/**
 * Fichier de mise à jour de la version 1.6.3 à la version 1.6.4 par défaut
 *
 *
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= msj_ok("Champ XXX ajouté avec succès");
 *
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.4(dev) :</h3>";

/*
// Section d'exemple

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'droits_acces_fichiers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'droits_acces_fichiers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS droits_acces_fichiers (
	id INT(11) unsigned NOT NULL auto_increment,
	fichier VARCHAR( 255 ) NOT NULL ,
	identite VARCHAR( 255 ) NOT NULL ,
	type VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}
*/

$result .= "&nbsp;-> Ajout d'un champ 'date_entree' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM eleves LIKE 'date_entree';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE eleves ADD date_entree DATETIME COMMENT 'Timestamp de sortie de l\'élève de l\'établissement (fin d\'inscription)';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Initialisation du terme 'incident' dans le module Discipline : ";
$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
if ($mod_disc_terme_incident=="") {
	if (!saveSetting("mod_disc_terme_incident", 'incident')) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("Ok !");
	}
} else {
	$result .= msj_present("déjà faite");
}

$result .= "&nbsp;-> Initialisation du terme 'sanction' dans le module Discipline : ";
$mod_disc_terme_sanction=getSettingValue('mod_disc_terme_sanction');
if ($mod_disc_terme_sanction=="") {
	if (!saveSetting("mod_disc_terme_sanction", 'sanction')) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("Ok !");
	}
} else {
	$result .= msj_present("déjà faite");
}

$result .= "&nbsp;-> Ajout d'un champ 'saisie_prof' à la table 's_types_sanctions2' : ";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_types_sanctions2 LIKE 'saisie_prof';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_types_sanctions2 ADD saisie_prof char(1) NOT NULL default 'n';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'saisie_par' à la table 's_sanctions' : ";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_sanctions LIKE 'saisie_par';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_sanctions ADD saisie_par varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'signature_droits' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'signature_droits'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS signature_droits (
		id INT(11) unsigned NOT NULL auto_increment,
		login VARCHAR( 255 ) NOT NULL ,
		PRIMARY KEY ( id )
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'signature_fichiers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'signature_fichiers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS signature_fichiers (
		id_fichier INT(11) unsigned NOT NULL auto_increment,
		fichier VARCHAR( 255 ) NOT NULL ,
		login VARCHAR( 255 ) NOT NULL ,
		type VARCHAR( 255 ) NOT NULL,
		PRIMARY KEY ( id_fichier )
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'signature_classes' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'signature_classes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS signature_classes (
		id INT(11) unsigned NOT NULL auto_increment,
		login VARCHAR( 255 ) NOT NULL ,
		id_classe INT( 11 ) NOT NULL ,
		id_fichier INT( 11 ) NOT NULL ,
		PRIMARY KEY ( id )
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

// Pour tester
// insert into setting set name='fichier_signature', value='signature.jpg';
// Et copier un signature.jpg dans "../backup/".getSettingValue('backup_directory')
if((getSettingValue('fichier_signature')!="")&&(file_exists("../backup/".getSettingValue('backup_directory')."/".getSettingValue('fichier_signature')))) {
	$result .= "<br /><strong>Modification de la gestion de la signature des bulletins : </strong><br />Transfert du fichier <a href='"."../backup/".getSettingValue('backup_directory')."/".getSettingValue('fichier_signature')."' target='_blank'>".getSettingValue('fichier_signature')."</a> pour votre usage personnel (<em>dans votre dossier temporaire</em>).<br />Pour modifier cela, voyez <a href='../gestion/gestion_signature.php'>Gestion des modules/Bulletins/Fichiers de signature</a>";
	$user_temp_directory=get_user_temp_directory();
	if(!$user_temp_directory) {
		$result.="<br /><span style='color:red'>Votre dossier temporaire n'est pas accessible.</span>";
	}
	else {
		$result.="<br />Déplacement du fichier &nbsp;: ";
		if(!file_exists("../temp/".$user_temp_directory."/signature/")) {
			if(mkdir("../temp/".$user_temp_directory."/signature/")) {
				$dir_sign_exist=true;
			}
			else {
				$result .= msj_erreur(" lors de la création de "."../temp/".$user_temp_directory."/signature/");
				$dir_sign_exist=false;
			}
		}
		else {
			$dir_sign_exist=true;
		}

		if($dir_sign_exist) {
			$ok=copy("../backup/".getSettingValue('backup_directory')."/".getSettingValue('fichier_signature'), "../temp/".$user_temp_directory."/signature/".getSettingValue('fichier_signature'));
			if($ok) {
				$result .= msj_ok("Ok !");

				$result.="Enregistrement du droit d'utiliser un fichier de signature&nbsp;: ";
				$sql="SELECT 1=1 FROM signature_droits WHERE login='".$_SESSION['login']."';";
				$test_droit=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_droit)==0) {
					$sql="INSERT INTO signature_droits SET login='".$_SESSION['login']."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$result .= msj_erreur();
					}
					else {
						$result .= msj_ok("Ok !");
					}
				}
				else {
					$result .= msj_present("déjà présent");
				}

				$result.="Enregistrement du nom de fichier dans 'signature_fichiers'&nbsp;: ";
				$sql="SELECT 1=1 FROM signature_fichiers WHERE login='".$_SESSION['login']."' AND fichier='".getSettingValue('fichier_signature')."';";
				$test_sf=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test_droit)==0) {
					$sql="INSERT INTO signature_fichiers SET login='".$_SESSION['login']."', fichier='".getSettingValue('fichier_signature')."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$result .= msj_erreur();
					}
					else {
						$result .= msj_ok("Ok !");

						$result .= "Suppression de la copie du fichier en backup : ";
						if(!unlink("../backup/".getSettingValue('backup_directory')."/".getSettingValue('fichier_signature'))) {
							$result .= msj_erreur();
						}
						else {
							$result .= msj_ok("Ok !");

							$sql="DELETE FROM setting WHERE name='fichier_signature';";
							$menage=mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
				}
				else {
					$result .= msj_present("déjà présent");

					$sql="DELETE FROM setting WHERE name='fichier_signature';";
					$menage=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
			else {
				$result .= msj_erreur(" lors de la copie du fichier de signature vers "."../temp/".$user_temp_directory."/signature/");
			}

		} else {
				$result .= msj_erreur();
		}
	}
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'ct_devoirs_faits' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'ct_devoirs_faits'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS ct_devoirs_faits (
	id INT(11) unsigned NOT NULL auto_increment,
	id_ct INT(11) unsigned NOT NULL,
	login VARCHAR( 255 ) NOT NULL ,
	etat VARCHAR( 50 ) NOT NULL,
	date_initiale DATETIME,
	date_modif DATETIME,
	commentaire VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'cn_conteneurs_modele' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'cn_conteneurs_modele'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cn_conteneurs_modele (
id_modele int(11) NOT NULL auto_increment, 
nom_court varchar(32) NOT NULL default '', 
description varchar(128) NOT NULL default '', 
PRIMARY KEY  (id_modele)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}


$result .= "<br />";
$result .= "<strong>Ajout d'une table 'cn_conteneurs_modele_conteneurs' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'cn_conteneurs_modele_conteneurs'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cn_conteneurs_modele_conteneurs (
id int(11) NOT NULL auto_increment, 
id_modele int(11) NOT NULL default '0', 
id_racine int(11) NOT NULL default '0', 
nom_court varchar(32) NOT NULL default '', 
nom_complet varchar(64) NOT NULL default '', 
description varchar(128) NOT NULL default '', 
mode char(1) NOT NULL default '2', 
coef decimal(3,1) NOT NULL default '1.0', 
arrondir char(2) NOT NULL default 's1', 
ponderation decimal(3,1) NOT NULL default '0.0', 
display_parents char(1) NOT NULL default '0', 
display_bulletin char(1) NOT NULL default '1', 
parent int(11) NOT NULL default '0', 
PRIMARY KEY  (id), 
INDEX parent_racine (parent,id_racine), 
INDEX racine_bulletin (id_racine,display_bulletin)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "Initialisation du droit pour un professeur de créer des ".getSettingValue('gepi_denom_boite')."s dans ses carnets de notes : ";
$test = sql_query1("SELECT 1=1 FROM setting WHERE name='GepiPeutCreerBoitesProf'");
if ($test == -1) {
	$result_inter = traite_requete("INSERT INTO setting SET name='GepiPeutCreerBoitesProf', value='yes';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le droit existe déjà (validé ou non)");
}

$result .= "&nbsp;-> Ajout d'un champ 'modele_id_conteneur' à la table 'cn_conteneurs' : ";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM cn_conteneurs LIKE 'modele_id_conteneur';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE cn_conteneurs ADD modele_id_conteneur int(11) NOT NULL default '0';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Initialisation du témoin d'activation/désactivation de la recherche de lapsus : ";
$active_recherche_lapsus=getSettingValue('active_recherche_lapsus');
if ($active_recherche_lapsus=="") {
	if (!saveSetting("active_recherche_lapsus", 'y')) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("Ok !");
	}
} else {
	$result .= msj_present("déjà faite");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'mod_alerte_divers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mod_alerte_divers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS mod_alerte_divers (
	id INT(11) unsigned NOT NULL auto_increment,
	name VARCHAR( 255 ) NOT NULL ,
	value VARCHAR( 255 ) NOT NULL ,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}


/*
 * mysql_query est obsolète depuis PHP 5.5.0, on utilise mysqli à la place
 */
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDb);
/* Modification du jeu de résultats en utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", $mysqli->error);
    die();
}


$result .= "<br />";
$result .= "<strong>Ajout d'un champ pour autoriser l'affichage des évaluations cumules pour les familles :</strong><br />";

$requete = $mysqli->query("SHOW COLUMNS FROM `cc_dev` LIKE 'vision_famille';");
if (!$requete->num_rows) {
    $requete2 = $mysqli->query("ALTER TABLE `cc_dev` ADD `vision_famille` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Autorisation de voir pour les familles';");
    if ($requete2 ) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("Échec !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}
$requete->close();


$result .= "<br />";
$result .= "<strong>Ajout d'un champ date d'affichage pour chaque évaluation cumule, pour les familles :</strong><br />";

$requete = $mysqli->query("SHOW COLUMNS FROM `cc_eval` LIKE 'vision_famille';");
if (!$requete->num_rows) {
    $requete2 = $mysqli->query("ALTER TABLE `cc_eval` ADD `vision_famille` DATE NOT NULL COMMENT 'Autorisation de voir pour les familles';");
    if ($requete2 ) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("Échec !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}
$requete->close();


$result .= "<br />";
$result .= "<strong>Ajout d'un champ mode pour l'ouverture exceptionnelle de modification/saisie d'appréciation du bulletin en période partiellement close. :</strong><br />";
$requete = $mysqli->query("SHOW COLUMNS FROM matieres_app_delais LIKE 'mode';");
if (!$requete->num_rows) {
    $requete2 = $mysqli->query("ALTER TABLE matieres_app_delais ADD mode VARCHAR(100) NOT NULL;");
    if ($requete2 ) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("Échec !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}
$requete->close();


$result .= "&nbsp;-> Initialisation de 'gepi_cpe_suivi' à la valeur 'C.P.E.' : ";
$gepi_cpe_suivi=getSettingValue('gepi_cpe_suivi');
if ($gepi_cpe_suivi=="") {
	if (!saveSetting("gepi_cpe_suivi", 'C.P.E.')) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("Ok !");
	}
} else {
	$result .= msj_present("déjà faite");
}
$result .= "<br />";

$result .= "<strong>Modularité de l'affichage du nom du C.P.E. sur les bulletins HTML&nbsp;:</strong> ";
if (getSettingValue("bull_affiche_abs_cpe")=="") {
	if (getSettingValue("bull_affiche_absences")=="y") {
		if (!saveSetting("bull_affiche_abs_cpe", "y")) {
			$result .= msj_erreur("ECHEC !");
		}
		else {
			$result .= msj_ok("SUCCES !");
		}
	}
	else {
		if (!saveSetting("bull_affiche_abs_cpe", "n")) {
			$result .= msj_erreur("ECHEC !");
		}
		else {
			$result .= msj_ok("SUCCES !");
		}
	}
}
else {
	$result .= msj_present("déjà faite");
}
$result .= "<strong>Modularité de l'affichage du nom du C.P.E. sur les bulletins PDF&nbsp;:</strong><br />";
$result .= "&nbsp;->Contrôle de la suppression d'anomalies sur les modèles de bulletins PDF&nbsp;: ";

	// nettoyage des valeurs vides
	//$sql = "SELECT * FROM `modele_bulletin` WHERE nom ='active_bloc_absence' ";
	$sql="DELETE FROM `modele_bulletin` WHERE nom ='afficher_abs_tot' AND valeur ='';";
	$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="DELETE FROM `modele_bulletin` WHERE nom ='afficher_abs_ret' AND valeur ='';";
	$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="DELETE FROM `modele_bulletin` WHERE nom ='afficher_abs_nj' AND valeur ='';";
	$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
	$sql="DELETE FROM `modele_bulletin` WHERE nom ='afficher_abs_cpe' AND valeur ='';";
	$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
$result .= msj_ok("SUCCES !");

// forcage à 1 de la valeur de afficher_abs_cpe si il existe afficher_abs_tot
$result .= "&nbsp;->Mise à niveau des modèles PDF existants &nbsp;: ";
$nb_changes = 0;
//On recherche les différents modeles
$sql="SELECT DISTINCT `id_model_bulletin` FROM `modele_bulletin`;";
$res_mod=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_mod)>0) {
	$result .= "&nbsp;->Mise à jour des modèles de bulletins PDF&nbsp;: ";
	while($lig_mod=mysqli_fetch_object($res_mod)) {
		//Si le modèle contient déja une valeur afficher_abs_cpe
		$sql = "SELECT `id_model_bulletin`, `nom`, `valeur` FROM `modele_bulletin` WHERE nom = 'afficher_abs_cpe' AND `id_model_bulletin`= $lig_mod->id_model_bulletin";
		//echo "$sql<br />";
		$res_cpe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_cpe)>0) continue; //on passe directement au modèle suivant
		//Si le modèle à des particularités d'absences pour ne pas changer l'affichage existant
		$sql = "SELECT `id_model_bulletin`, `nom`, `valeur` FROM `modele_bulletin` WHERE nom = 'afficher_abs_tot' AND `id_model_bulletin`= $lig_mod->id_model_bulletin";
		//echo "$sql<br />";
		$res_cpe=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_cpe)>0)
		{//selon qu'il affiche ou pas le bloc absences
			$sql = "SELECT `id_model_bulletin`, `nom`, `valeur` FROM `modele_bulletin` WHERE nom = 'affiche_bloc_absences' AND `id_model_bulletin`= $lig_mod->id_model_bulletin";
			//echo "$sql<br />";
			$test_resp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_resp)>0) { //on force afficher_abs_cpe à 1
				$sql="INSERT INTO `modele_bulletin` SET `nom` ='afficher_abs_cpe', `valeur` ='1', `id_model_bulletin` ='$lig_mod->id_model_bulletin';";
				//echo "$sql<br />";
				$query=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_changes++;
			}
			else { //sinon on force afficher_abs_cpe à 0
				$sql="INSERT INTO `modele_bulletin` SET `nom` ='afficher_abs_cpe', `valeur` ='0', `id_model_bulletin` ='$lig_mod->id_model_bulletin';";
				//echo "$sql<br />";
				$query=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_changes++;
			}
		}
	}
}
$res_mod->close();
if ($nb_changes != 0) {
    $result .= msj_ok($nb_changes." modèles de bulletins ont été mis à niveau");
}
else {
    $result .= msj_present("déjà à niveau");
}

?>
