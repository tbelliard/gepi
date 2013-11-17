<?php
/**
 * Fichier de mise à jour de la version 1.6.1 à la version 1.6.2 par défaut
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
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.2 :</h3>";

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'tel_port' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM eleves LIKE 'tel_port';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE eleves ADD tel_port varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'tel_prof' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM eleves LIKE 'tel_prof';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE eleves ADD tel_prof varchar(255) NOT NULL default '';");
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

$result .= "<br />&nbsp;-> Ajout d'un champ 'id_nature_sanction' à la table 's_sanctions'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_sanctions LIKE 'id_nature_sanction';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_sanctions ADD id_nature_sanction int(11) NOT NULL AFTER nature;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_sanctions LIKE 'id_nature_sanction';"));
if ($test_champ>0) {
	// On ajoute une table parce qu'on pourrait avoir une collision sur la migration avec une sanction autre de l'ancienne table s_types_sanctions que quelqu'un aurait eu l'idée de créer avec un des noms réservés (Retenue, Exclusion, Travail) parce qu'il manquait un test pour interdire cette bizarrerie.
	$result .= "<br />";
	$result .= "<strong>Ajout d'une table 's_types_sanctions2' :</strong><br />";
	$test = sql_query1("SHOW TABLES LIKE 's_types_sanctions2'");
	if ($test == -1) {
		$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS s_types_sanctions2 (id_nature INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,nature VARCHAR( 255 ) NOT NULL ,type VARCHAR( 255 ) NOT NULL DEFAULT 'autre') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");

			$tab_nature_sanction=array("Exclusion", "Retenue", "Travail");
			for($loop=0;$loop<count($tab_nature_sanction);$loop++) {
				$sql="SELECT id_nature FROM s_types_sanctions2 WHERE nature='".$tab_nature_sanction[$loop]."';";
				$res_ns=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ns)==0) {
					$result_inter = traite_requete("INSERT INTO s_types_sanctions2 SET nature='".$tab_nature_sanction[$loop]."', type='".mb_strtolower($tab_nature_sanction[$loop])."';");
					if ($result_inter == '') {
						$id_nature_sanction_courante=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

						// La nature était en minuscule dans s_sanctions et il faut maintenant qu'elle coïncide avec la casse de s_types_sanctions2.nature (donc avec une initiale en majuscule)
						$sql="UPDATE s_sanctions SET id_nature_sanction='$id_nature_sanction_courante', nature='".$tab_nature_sanction[$loop]."' WHERE nature='".$tab_nature_sanction[$loop]."';";
						$result_inter = traite_requete($sql);
						if ($result_inter == '') {
							$result.="&nbsp;-> Mise à jour des sanctions ".$tab_nature_sanction[$loop]." existantes : ".msj_ok("Ok !");
						}
						else {
							$result.="&nbsp;-> Mise à jour des sanctions ".$tab_nature_sanction[$loop]." existantes : ".msj_erreur("Erreur !");
						}
					} else {
						$result.=msj_erreur("Définition du type de sanction ".$tab_nature_sanction[$loop]." : Erreur !");
					}
				}
				else {
					//$tab_id_nature_sanction[$tab_nature_sanction[$loop]]=mysql_result($res_ns, 0, "id_nature");
					$id_nature_sanction_courante=mysql_result($res_ns, 0, "id_nature");

					// La nature était en minuscule dans s_sanctions et il faut maintenant qu'elle coïncide avec la casse de s_types_sanctions2.nature (donc avec une initiale en majuscule)
					$sql="UPDATE s_sanctions SET id_nature_sanction='$id_nature_sanction_courante', nature='".$tab_nature_sanction[$loop]."' WHERE nature='".$tab_nature_sanction[$loop]."';";
					$result_inter = traite_requete($sql);
					if ($result_inter == '') {
						$result.="&nbsp;-> Mise à jour des sanctions ".$tab_nature_sanction[$loop]." existantes : ".msj_ok("Ok !");
					}
					else {
						$result.="&nbsp;-> Mise à jour des sanctions ".$tab_nature_sanction[$loop]." existantes : ".msj_erreur("Erreur !");
					}
				}
			}

			/*
			$sql="INSERT INTO s_types_sanctions2 (SELECT '', nature, 'autre' FROM s_types_sanctions ORDER BY nature);";
			$res_sts=mysql_query($sql);
			*/
			$sql="SELECT * FROM s_types_sanctions;";
			$res_sts=mysqli_query($GLOBALS["mysqli"], $sql);
			while($lig_sts=mysqli_fetch_object($res_sts)) {
				$sql="INSERT INTO s_types_sanctions2 SET nature='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_sts->nature) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."', type='autre';";
				$result_inter = traite_requete($sql);
				if ($result_inter == '') {
					$id_nature_sanction=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

					$sql="update s_sanctions set id_nature_sanction='$id_nature_sanction', nature='".((isset($GLOBALS["mysqli"]) && is_object($GLOBALS["mysqli"])) ? mysqli_real_escape_string($GLOBALS["mysqli"], $lig_sts->nature) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."' where id_sanction in (select id_sanction from s_autres_sanctions where id_nature='".$lig_sts->id_nature."');";
					$result_inter = traite_requete($sql);
					if ($result_inter == '') {
						$result.="&nbsp;-> Mise à jour des sanctions ".$lig_sts->nature." existantes : ".msj_ok("Ok !");
					}
					else {
						$result.="&nbsp;-> Mise à jour des sanctions ".$lig_sts->nature." existantes : ".msj_erreur("Erreur !");
					}
				}
				else {
					$result.="&nbsp;-> Re-déclaration de la nature de sanction ".$lig_sts->nature." : ".msj_erreur("Erreur !");
				}
			}

		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	} else {
		$result .= msj_present("La table existe déjà");
	}
}

// Initialisation d'une nouvelle variable:
if(getSettingValue('gepi_en_production')=="") {
	if(!saveSetting('gepi_en_production', 'y')) {
		$result .= "Initialisation d'un témoin comme quoi le serveur Gepi n'est pas juste un serveur de test, mais un serveur en production : ".msj_erreur("ECHEC !");
	}
	else {
		$result .= "Initialisation d'un témoin comme quoi le serveur Gepi n'est pas juste un serveur de test, mais un serveur en production : ".msj_ok("Ok !");
	}
}

if(getSettingValue('GepiAccesCDTToutesClasses')=="") {
	if(!saveSetting('GepiAccesCDTToutesClasses', 'yes')) {
		$result .= "Initialisation d'une variable : Accès des professeurs aux cahiers de textes de toutes les classes : ".msj_erreur("ECHEC !");
	}
	else {
		$result .= "Initialisation d'une variable : Accès des professeurs aux cahiers de textes de toutes les classes : ".msj_ok("Ok !");
	}
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'log_maj_sconet' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'log_maj_sconet'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE log_maj_sconet (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	login VARCHAR( 50 ) NOT NULL ,
	texte TEXT NOT NULL ,
	date_debut DATETIME NOT NULL ,
	date_fin DATETIME NOT NULL
	) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Module discipline :</strong><br />";
$result .= "&nbsp;-> Ajout d'un champ 'primo_declarant' à la table 's_incidents'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_incidents LIKE 'primo_declarant';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_incidents ADD primo_declarant varchar(50);");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'materiel' à la table 's_travail_mesure'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_travail_mesure LIKE 'materiel';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_travail_mesure ADD materiel varchar(150);");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'materiel' à la table 's_retenues'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_retenues LIKE 'materiel';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_retenues ADD materiel varchar(150);");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}


$result .= "<br />";
$result .= "<strong>Ajout d'une table 'classes_param' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'classes_param';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE classes_param (
										id int(11) NOT NULL AUTO_INCREMENT,
										id_classe smallint(6) NOT NULL,
										name varchar(100) NOT NULL,
										value varchar(255) NOT NULL,
										PRIMARY KEY (id),
										UNIQUE KEY id_classe_name (id_classe,name)
										) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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

$result .= "&nbsp;->Modification du type du champ 'cp' de la table 'etablissements' de 'int' en 'varchar' :";
$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE etablissements CHANGE cp cp VARCHAR( 10 ) NOT NULL;");
if ($query) {
	$result .= msj_ok("SUCCES !");
} else {
	$result .= msj_erreur("ECHEC !");
}
$result .= "<br />";

$result .= "<strong>Bulletins :</strong><br />";
$result .= "&nbsp;->Prise en compte du module Bulletins : ";
$test = sql_query1("SELECT 1=1 FROM setting WHERE name='active_bulletins';");
if ($test == -1) {
	$result_inter = traite_requete("INSERT INTO setting SET name='active_bulletins', value='y';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Prise en compte déjà effectuée.");
}
$result .= "<br />";

$result .= "<strong>Droits :</strong><br />";
$result .= "&nbsp;->Initialisation de l'accès responsable à la colonne Moyenne de la classe sur les bulletins simplifiés : ";
$test = sql_query1("SELECT 1=1 FROM setting WHERE name='GepiAccesBulletinSimpleColonneMoyClasseResp';");
if ($test == -1) {
	$result_inter = traite_requete("INSERT INTO setting SET name='GepiAccesBulletinSimpleColonneMoyClasseResp', value='y';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Prise en compte déjà effectuée.");
}
$result .= "<br />";

$result .= "&nbsp;->Initialisation de l'accès élève à la colonne Moyenne de la classe sur les bulletins simplifiés : ";
$test = sql_query1("SELECT 1=1 FROM setting WHERE name='GepiAccesBulletinSimpleColonneMoyClasseEleve';");
if ($test == -1) {
	$result_inter = traite_requete("INSERT INTO setting SET name='GepiAccesBulletinSimpleColonneMoyClasseEleve', value='y';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Prise en compte déjà effectuée.");
}
$result .= "<br />";

$result .= "&nbsp;->Initialisation de l'affichage du volume des documents joints aux CDT : ";
$cdt_afficher_volume_docs_joints=getSettingValue('cdt_afficher_volume_docs_joints');
if($cdt_afficher_volume_docs_joints=='') {
	$result_inter = traite_requete("INSERT INTO setting SET name='cdt_afficher_volume_docs_joints', value='y';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Prise en compte déjà effectuée.");
}
$result .= "<br />";

$result .= "&nbsp;->Initialisation de l'affichage des moyennes classe sur les graphes en courbe : ";
$cdt_afficher_volume_docs_joints=getSettingValue('graphe_affiche_moy_classe');
if($cdt_afficher_volume_docs_joints=='') {
	$result_inter = traite_requete("INSERT INTO setting SET name='graphe_affiche_moy_classe', value='oui';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Prise en compte déjà effectuée.");
}
$result .= "<br />";

/*
$result .= "<strong>Ajout d'une table 'responsabilite_plus' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'responsabilite_plus'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS responsabilite_plus (ele_id varchar(10) NOT NULL, pers_id varchar(10) NOT NULL, acces varchar(1) NOT NULL, INDEX pers_id ( pers_id ), INDEX ele_id ( ele_id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
*/

$result .= "&nbsp;-> Ajout d'un champ 'acces_sp' à la table 'responsables2'<br />";
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM responsables2 LIKE 'acces_sp';"));
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE responsables2 ADD acces_sp varchar(1) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "Initialisation du mode de calcul de moyenne quand il y a des ".getSettingValue('gepi_denom_boite')."s dans les carnets de notes : ";
$test = sql_query1("SELECT 1=1 FROM setting WHERE name='cnBoitesModeMoy'");
if ($test == -1) {
	$result_inter = traite_requete("INSERT INTO setting SET name='cnBoitesModeMoy', value='2';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Déjà faite.");
}

?>
