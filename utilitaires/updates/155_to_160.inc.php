<?php
/**
 * Fichier de mise à jour de la version 1.5.5 à la version 1.6.0
 * 
 * $Id$
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.0 </h3>";

if ($version_old<="1.6.0") require dirname(__FILE__).'/ISO_to_UTF8.inc.php';

$result.="<br />";
$result.="<strong>Module relevé de notes :</strong>";
$result.="<br />";

$result .= "&nbsp;-> Ajout d'un champ rn_abs_2 à la table 'classes'<br />";
// Ajout d'une colonne rn_abs_2 dans classes pour stocker l'affichage ou non des absences sur les relevés de notes
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM classes LIKE 'rn_abs_2';"));

	// $result .= "&nbsp;-> Place du champ rn_abs_2 dans la table 'classes' : ".$test_champ."<br />";
if ($test_champ==0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE classes ADD rn_abs_2 char(1) NOT NULL default 'n';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}
	
$result .= "<br /><strong>Table abs2 agrégation</strong><br />";
//correction d'une erreur de mise à jour précédente
$result .= "&nbsp;->Recréation de la structure de la table d'agrégation<br />";
$query = mysqli_query($GLOBALS["mysqli"], "DROP TABLE IF EXISTS a_agregation_decompte;");
if ($query) {
		$result .= msj_ok();
} else {
		$result .= msj_erreur(mysqli_error($GLOBALS["mysqli"]));
}

$query = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE a_agregation_decompte
(
	eleve_id INTEGER(11) NOT NULL COMMENT 'id de l\'eleve',
	date_demi_jounee DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT 'Date de la demi journée agrégée : 00:00 pour une matinée, 12:00 pour une après midi',
	manquement_obligation_presence TINYINT DEFAULT 0 COMMENT 'Cette demi journée est comptée comme absence',
	non_justifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une justification',
	notifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une notification à la famille',
	retards INTEGER DEFAULT 0 COMMENT 'Nombre de retards total décomptés dans la demi journée',
	retards_non_justifies INTEGER DEFAULT 0 COMMENT 'Nombre de retards non justifiés décomptés dans la demi journée',
	motifs_absences TEXT COMMENT 'Liste des motifs (table a_motifs) associés à cette demi-journée d\'absence',
	motifs_retards TEXT COMMENT 'Liste des motifs (table a_motifs) associés aux retard de cette demi-journée',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (eleve_id,date_demi_jounee),
	CONSTRAINT a_agregation_decompte_FK_1
		FOREIGN KEY (eleve_id)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Table d\'agregation des decomptes de demi journees d\'absence et de retard';");
if ($query) {
		$result .= msj_ok();
} else {
		$result .= msj_erreur(mysqli_error($GLOBALS["mysqli"]));
}


$result .= "<br />";
$result .= "<strong>Ajout d'une table 'temp_abs_import' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'temp_abs_import'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS temp_abs_import (
		id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		login varchar(50) NOT NULL default '',
		cpe_login varchar(50) NOT NULL default '',
		elenoet varchar(50) NOT NULL default '',
		libelle varchar(50) NOT NULL default '',
		nbAbs INT(11) NOT NULL default '0',
		nbNonJustif INT(11) NOT NULL default '0',
		nbRet INT(11) NOT NULL default '0',
		UNIQUE KEY elenoet (elenoet)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");

	/*
		Une table du même nom avait précédemment la structure suivante:

		CREATE TABLE IF NOT EXISTS $temp_table_abs (
			id INT(11) not null auto_increment,
			login VARCHAR(50) not null,
			cpe_login VARCHAR(50) not null,
			nbret INT(11) not null,
			nbabs INT(11) not null,
			nbnj INT(11) not null,
			primary key (id));
	*/

	$res = mysqli_query($GLOBALS["mysqli"], 'select * from temp_abs_import LIMIT 1;');
	$numOfCols = (($___mysqli_tmp = mysqli_num_fields($res)) ? $___mysqli_tmp : false);
	// Même si la table est vide, on récupère bien la liste des champs
	//$result .= "Nombre de colonnes dans la table 'temp_abs_import' : $numOfCols<br />";
	//$result .= "Nombre d'enregistrements dans la table 'temp_abs_import' : ".mysql_num_rows($res)."<br />";
	for($i=0;$i<$numOfCols;$i++) {
		//$result .= mysql_field_name($res, $i) . "<br />\n";
		$nom_du_champ=((($___mysqli_tmp = mysqli_fetch_field_direct($res, 0)->name) && (!is_null($___mysqli_tmp))) ? $___mysqli_tmp : false);
		if($nom_du_champ=='nbret') {
			$result .= "&nbsp;-> Renommage du champ '$nom_du_champ' en 'nbRet' dans la table 'temp_abs_import'<br />";
			$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import CHANGE nbret nbRet INT(11) NOT NULL default '0';");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		}
		elseif($nom_du_champ=='nbabs') {
			$result .= "&nbsp;-> Renommage du champ '$nom_du_champ' en 'nbAbs' dans la table 'temp_abs_import'<br />";
			$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import CHANGE nbabs nbAbs INT(11) NOT NULL default '0';");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		}
	}

	// Normalement, l'ajout ci-dessous correspond à une très vieille version de la table:
	$result .= "&nbsp;-> Ajout d'un champ 'cpe_login' à la table 'temp_abs_import'<br />";
	$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM temp_abs_import LIKE 'cpe_login';"));
	if ($test_champ==0) {
		$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import ADD cpe_login varchar(50) NOT NULL default '';");
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}
	} else {
		$result .= msj_present("Le champ existe déjà");
	}

	$result .= "&nbsp;-> Ajout d'un champ 'libelle' à la table 'temp_abs_import'<br />";
	$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM temp_abs_import LIKE 'libelle';"));
	if ($test_champ==0) {
		$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import ADD libelle varchar(50) NOT NULL default '';");
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}
	} else {
		$result .= msj_present("Le champ existe déjà");
	}

	$result .= "&nbsp;-> Ajout d'un champ 'elenoet' à la table 'temp_abs_import'<br />";
	$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM temp_abs_import LIKE 'elenoet';"));
	if ($test_champ==0) {
		$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import ADD elenoet varchar(50) NOT NULL default '';");
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}
	} else {
		$result .= msj_present("Le champ existe déjà");
	}

	$result .= "&nbsp;-> Test du champ 'nbNonJustif' dans la table 'temp_abs_import'<br />";
	$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM temp_abs_import LIKE 'nbnj';"));
	if ($test_champ==0) {
		$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM temp_abs_import LIKE 'nbNonJustif';"));
		if ($test_champ==0) {
			$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import ADD nbNonJustif INT(11) NOT NULL default '0';");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Le champ existe déjà");
		}
	}
	else {
		$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM temp_abs_import LIKE 'nbNonJustif';"));
		if ($test_champ==0) {
			$result .= "Renommage du champ 'nbnj' en 'nbNonJustif'&nbsp;: ";
			$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import CHANGE nbnj nbNonJustif INT(11) NOT NULL default '0';");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		}
		else {
			$result .= "Suppression de l'ancien champ 'nbnj' &nbsp;: ";
			$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE temp_abs_import DROP nbnj;");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		}
	}
}


$result .= "<br />";
$req_test=mysqli_query($GLOBALS["mysqli"], "SELECT value FROM setting WHERE name = 'utiliserMenuBarre'");
$res_test=mysqli_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('utiliserMenuBarre', 'yes');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre utiliserMenuBarre : Ok !");
  } else {
    $result.=msj_erreur("Définition du paramètre utiliserMenuBarre : Erreur !");
  }
} else {
  $result .= msj_present("Le paramètre utiliserMenuBarre existe déjà dans la table setting.");
}

$result .= "<br />";
$result .= "<strong>Test des formats de login</strong><br />";
$tab_formats_login_a_tester=array('mode_generation_login', 'mode_generation_login_eleve', 'mode_generation_login_responsable');
for($loop=0;$loop<count($tab_formats_login_a_tester);$loop++) {
	$valeur_current_mode_generation_login=getSettingValue($tab_formats_login_a_tester[$loop]);
	if(!check_format_login($valeur_current_mode_generation_login)) {
		$sql="SELECT * FROM infos_actions WHERE titre='Format des logins générés';";
		$test_ia=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_ia)==0) {
			enregistre_infos_actions("Format des logins générés","Le format des logins générés par Gepi pour les différentes catégories d'utilisateurs doit être contrôlé et revalidé dans la page <a href='./gestion/param_gen.php#format_login_pers'>Configuration générale</a>",array("administrateur"),'statut');
		}

		$result .= "Format de login ";
		if($tab_formats_login_a_tester[$loop]=='mode_generation_login') {$result .= "<b>personnels</b>";}
		elseif($tab_formats_login_a_tester[$loop]=='mode_generation_login_eleve') {$result .= "<b>élèves</b>";}
		elseif($tab_formats_login_a_tester[$loop]=='mode_generation_login_responsable') {$result .= "<b>responsables</b>";}

		$result .= " invalide : $valeur_current_mode_generation_login<br />";
		if($valeur_current_mode_generation_login=="name") {
			$result .= "Conversion en 'nnnnnnnnnnnnnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnnnnnnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name8") {
			$result .= "Conversion en 'nnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="fname19") {
			$result .= "Conversion en 'pnnnnnnnnnnnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'pnnnnnnnnnnnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif(($valeur_current_mode_generation_login=="firstdotname")||($valeur_current_mode_generation_login=="lcs")) {
			$result .= "Conversion en 'pppppppppppppppppppp.nnnnnnnnnnnnnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'pppppppppppppppppppp.nnnnnnnnnnnnnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="firstdotname19") {
			$result .= "Conversion en 'pppppppppp.nnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'pppppppppp.nnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="namef8") {
			$result .= "Conversion en 'nnnnnnnp' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnp')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name9_p") {
			$result .= "Conversion en 'nnnnnnnnn_p' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnn_p')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name9-p") {
			$result .= "Conversion en 'nnnnnnnnn-p' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnn-p')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name9.p") {
			$result .= "Conversion en 'nnnnnnnnn.p' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnn.p')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="p_name9") {
			$result .= "Conversion en 'p_nnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'p_nnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="p-name9") {
			$result .= "Conversion en 'p-nnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'p-nnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="p.name9") {
			$result .= "Conversion en 'p.nnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'p.nnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name9_ppp") {
			$result .= "Conversion en 'nnnnnnnnn_ppp' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnn_ppp')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name9-ppp") {
			$result .= "Conversion en 'nnnnnnnnn-ppp' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnn-ppp')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="name9.ppp") {
			$result .= "Conversion en 'nnnnnnnnn.ppp' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'nnnnnnnnn.ppp')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="ppp_name9") {
			$result .= "Conversion en 'ppp_nnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'ppp_nnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="ppp-name9") {
			$result .= "Conversion en 'ppp_nnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'ppp-nnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		elseif($valeur_current_mode_generation_login=="ppp.name9") {
			$result .= "Conversion en 'ppp_nnnnnnnnn' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], 'ppp.nnnnnnnnn')) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
		else {
			if($tab_formats_login_a_tester[$loop]=='mode_generation_login') {
				$format_login="nnnnnnnp";
			}
			elseif($tab_formats_login_a_tester[$loop]=='mode_generation_login_eleve') {
				$format_login="nnnnnnnnn_p";
			}
			elseif($tab_formats_login_a_tester[$loop]=='mode_generation_login_responsable') {
				$format_login="nnnnnnnnn.p";
			}

			$result .= "Conversion en '$format_login' : ";
			if(saveSetting($tab_formats_login_a_tester[$loop], "$format_login")) {$result .= msj_ok("Ok !");} else {$result .= msj_erreur("ECHEC");}
		}
	}
}

$result .= "<br />";
$req_test=mysqli_query($GLOBALS["mysqli"], "SELECT 1=1 FROM ct_types_documents WHERE extension='ggb';");
$res_test=mysqli_num_rows($req_test);
if ($res_test==0){
  $result.="Ajout de GGB (GeoGebra) à la liste des extensions autorisées pour les fichiers joints aux cahiers de textes : ";
  $result_inter = traite_requete("INSERT INTO ct_types_documents SET titre='GeoGebra', extension='ggb', upload='oui';");
  if ($result_inter == '') {
    $result.=msj_ok("Ok !");
  } else {
    $result.=msj_erreur("Erreur !");
  }
}

$sql="SELECT 1=1 FROM ref_wiki WHERE ref='enseignement_invisible';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO ref_wiki VALUES ('','enseignement_invisible', 'http://www.sylogix.org/projects/gepi/wiki/Enseignement_invisible');";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
}
else {
	$sql="UPDATE ref_wiki SET url='http://www.sylogix.org/projects/gepi/wiki/Enseignement_invisible' WHERE ref='enseignement_invisible'";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
}

$result.="<br />";
$result.="Contrôle des index de la table absences&nbsp;: ";
$sql="show index from absences where sub_part!='NULL';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)!=0) {
  $result.="Correction des index de la table absences&nbsp;: ";
  $result_inter = traite_requete("ALTER TABLE absences DROP PRIMARY KEY , ADD PRIMARY KEY ( login , periode );");
  if ($result_inter == '') {
    $result.=msj_ok("Ok !");
  } else {
    $result.=msj_erreur("Erreur !");
  }
}
else {
  $result .= msj_present("Déjà correct");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 't_plan_de_classe' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 't_plan_de_classe'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS t_plan_de_classe (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	id_groupe INT(11) NOT NULL ,
	login_prof VARCHAR(50) NOT NULL ,
	dim_photo INT(11) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 't_plan_de_classe_ele' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 't_plan_de_classe_ele'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS t_plan_de_classe_ele (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	id_plan INT( 11 ) NOT NULL,
	login_ele VARCHAR(50) NOT NULL ,
	x INT(11) NOT NULL ,
	y INT(11) NOT NULL);";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'rss_users' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'rss_users'");
if ($test == -1) {
	$sql="CREATE TABLE rss_users (id int(11) NOT NULL auto_increment, user_login varchar(30) NOT NULL, user_uri varchar(30) NOT NULL, PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'ldap_bx' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'ldap_bx'");
if ($test == -1) {
	$sql="CREATE TABLE ldap_bx (
			id INT( 11 ) NOT NULL AUTO_INCREMENT ,
			login_u VARCHAR( 200 ) NOT NULL ,
			nom_u VARCHAR( 200 ) NOT NULL ,
			prenom_u VARCHAR( 200 ) NOT NULL ,
			statut_u VARCHAR( 50 ) NOT NULL ,
			identite_u VARCHAR( 50 ) NOT NULL ,
			PRIMARY KEY ( id )
			) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result.="<br />Fin mise à jour<br/>";
?>
