<?php
/**
 * Fichier de mise à jour de la version 1.7.0 à la version 1.7.1 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.7.1 :</h3>";

/*
// Section d'exemple

// Attention : on peut effectuer des mysqli_query() pour des tests en SELECT,
//             mais toujours utiliser traite_requete() pour les CREATE, ALTER, INSERT, UPDATE
//             pour que le message indiquant qu'il s'est produit une erreur soit affiché en haut de la page (l'admin ne lit pas toute la page;)

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$sql="ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
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

// Merci de ne pas enlever le témoin ci-dessous de "fin de section exemple"
// Fin SECTION EXEMPLE
*/

$result .= "&nbsp;-> Ajout d'un champ 'resumeBulletin' à la table 'aid'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM aid LIKE 'resumeBulletin';"));
if ($test_champ==0) {
	$sql="ALTER TABLE `aid` ADD `resumeBulletin` VARCHAR(1) NOT NULL COMMENT 'Y si le résumé doit être affiché sur le bulletin' AFTER `resume`;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT 1=1 FROM groupes_types WHERE nom_court='local';"));
if ($test_champ==0) {
	$result .= "Ajout du type 'local' pour les enseignements&nbsp;: ";
	$sql="INSERT INTO groupes_types SET nom_court='local', nom_complet='Enseignement local', nom_complet_pluriel='Enseignements locaux';";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
}


$result .= "<br />";
$result .= "<strong>Ajout d'une table 'engagements_droit_saisie' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'engagements_droit_saisie'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS engagements_droit_saisie (
	id INT(11) unsigned NOT NULL auto_increment,
	id_engagement INT(11) NOT NULL ,
	login VARCHAR( 50 ) NOT NULL DEFAULT '', 
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
$result .= "<br />";
$result .= "<strong>Ajout d'une table 'edt_cours_remplacements' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_cours_remplacements'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE edt_cours_remplacements (id_cours int(11) NOT NULL auto_increment, 
	id_groupe varchar(10) NOT NULL, 
	id_aid varchar(10) NOT NULL, 
	id_salle varchar(3) NOT NULL, 
	jour_semaine varchar(10) NOT NULL, 
	id_definie_periode varchar(3) NOT NULL, 
	duree varchar(10) NOT NULL default '2', 
	heuredeb_dec varchar(3) NOT NULL default '0', 
	id_semaine varchar(10) NOT NULL default '0', 
	id_calendrier varchar(3) NOT NULL default '0', 
	modif_edt varchar(3) NOT NULL default '0', 
	login_prof varchar(50) NOT NULL, 
	id_absence int(11) NOT NULL, 
	jour varchar(10) NOT NULL, DEFAULT '', 
	PRIMARY KEY  (id_cours)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

duree='".$duree."', 
heuredeb_dec='".$heuredeb_dec."'
*/

$result .= "&nbsp;-> Ajout d'un champ 'duree' à la table 'abs_prof_remplacement'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'duree';"));
if ($test_champ==0) {
	$sql="ALTER TABLE abs_prof_remplacement ADD duree varchar(10) NOT NULL default '0';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'heuredeb_dec' à la table 'abs_prof_remplacement'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'heuredeb_dec';"));
if ($test_champ==0) {
	$sql="ALTER TABLE abs_prof_remplacement ADD heuredeb_dec varchar(3) NOT NULL default '0';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'jour_semaine' à la table 'abs_prof_remplacement'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'jour_semaine';"));
if ($test_champ==0) {
	$sql="ALTER TABLE abs_prof_remplacement ADD jour_semaine varchar(10) NOT NULL;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'id_cours_remplaced' à la table 'abs_prof_remplacement'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'id_cours_remplaced';"));
if ($test_champ==0) {
	$sql="ALTER TABLE abs_prof_remplacement ADD id_cours_remplaced INT(11) NOT NULL;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$sql="SELECT DISTINCT id_statut FROM droits_speciaux;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig=mysqli_fetch_object($res)) {
	// Tester si les entrées '/mod_alerte/form_message.php', '/eleves/ajax_consultation.php' sont présentes
	$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/mod_alerte/form_message.php' AND id_statut='".$lig->id_statut."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		$result .= "&nbsp;-> Ajout du droit d'accès à '/mod_alerte/form_message.php' pour le statut autre n°".$lig->id_statut."&nbsp;: ";
		$sql="INSERT INTO droits_speciaux SET nom_fichier='/mod_alerte/form_message.php', id_statut='".$lig->id_statut."', autorisation='V';";
		//echo "$sql<br />";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	}
	$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/eleves/ajax_consultation.php' AND id_statut='".$lig->id_statut."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		$result .= "&nbsp;-> Ajout du droit d'accès à '/eleves/ajax_consultation.php' pour le statut autre n°".$lig->id_statut."&nbsp;: ";
		$sql="INSERT INTO droits_speciaux SET nom_fichier='/eleves/ajax_consultation.php', id_statut='".$lig->id_statut."', autorisation='V';";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	}

	$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/groupes/popup.php' AND id_statut='".$lig->id_statut."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/aid/popup.php' AND id_statut='".$lig->id_statut."';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$result .= "&nbsp;-> Ajout du droit d'accès à '/aid/popup.php' pour le statut autre n°".$lig->id_statut."&nbsp;: ";
			$sql="INSERT INTO droits_speciaux SET nom_fichier='/aid/popup.php', id_statut='".$lig->id_statut."', autorisation='V';";
			$result_inter = traite_requete($sql);
			if ($result_inter == '') {
				$result .= msj_ok("SUCCES !");
			}
			else {
				$result .= msj_erreur("ECHEC !");
			}
		}
	}

	$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/mod_abs2/enregistrement_saisie_groupe.php' AND id_statut='".$lig->id_statut."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/mod_abs2/visu_traitement.php' AND id_statut='".$lig->id_statut."';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$result .= "&nbsp;-> Ajout du droit d'accès à '/mod_abs2/visu_traitement.php' pour le statut autre n°".$lig->id_statut."&nbsp;: ";
			$sql="INSERT INTO droits_speciaux SET nom_fichier='/mod_abs2/visu_traitement.php', id_statut='".$lig->id_statut."', autorisation='V';";
			$result_inter = traite_requete($sql);
			if ($result_inter == '') {
				$result .= msj_ok("SUCCES !");
			}
			else {
				$result .= msj_erreur("ECHEC !");
			}
		}
	}

	$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/cahier_notes/visu_toutes_notes2.php' AND id_statut='".$lig->id_statut."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/visualisation/draw_graphe.php' AND id_statut='".$lig->id_statut."';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$result .= "&nbsp;-> Ajout du droit d'accès à '/visualisation/draw_graphe.php' pour le statut autre n°".$lig->id_statut."&nbsp;: ";
			$sql="INSERT INTO droits_speciaux SET nom_fichier='/visualisation/draw_graphe.php', id_statut='".$lig->id_statut."', autorisation='V';";
			$result_inter = traite_requete($sql);
			if ($result_inter == '') {
				$result .= msj_ok("SUCCES !");
			}
			else {
				$result .= msj_erreur("ECHEC !");
			}
		}
	}

	$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/edt/index2.php' AND id_statut='".$lig->id_statut."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="SELECT * FROM droits_speciaux WHERE nom_fichier='/edt/index2.php' AND id_statut='".$lig->id_statut."';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$result .= "&nbsp;-> Ajout du droit d'accès à '/lib/ajax_action.php' pour le statut autre n°".$lig->id_statut."&nbsp;: ";
			$sql="INSERT INTO droits_speciaux SET nom_fichier='/lib/ajax_action.php', id_statut='".$lig->id_statut."', autorisation='V';";
			$result_inter = traite_requete($sql);
			if ($result_inter == '') {
				$result .= msj_ok("SUCCES !");
			}
			else {
				$result .= msj_erreur("ECHEC !");
			}
		}
	}
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'a_droits' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'a_droits'");
if ($test == -1) {
//		$sql="SELECT 1=1 FROM a_droits WHERE login='".$_SESSION['login']."' AND page='/mod_abs2/admin/admin_types_absences.php' AND consultation='y'";
	$result_inter = traite_requete("CREATE TABLE a_droits (id int(11) NOT NULL auto_increment, 
	login varchar(50) NOT NULL, 
	page varchar(255) NOT NULL, 
	consultation varchar(10) NOT NULL DEFAULT 'n', 
	saisie varchar(10) NOT NULL DEFAULT 'n', 
	PRIMARY KEY (id), INDEX login_page (login,page)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_composantes'");
if ($test != -1) {
	$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM socle_eleves_composantes LIKE 'periode';"));
	if ($test_champ==0) {
		$result .= "&nbsp;-> Suppression de la table 'socle_eleves_composantes' avec son ancienne structure&nbsp;: ";
		$result_inter = traite_requete("DROP TABLE socle_eleves_composantes;");
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	}
}

$result .= "<strong>Ajout d'une table 'socle_eleves_composantes' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_composantes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE socle_eleves_composantes (id int(11) NOT NULL auto_increment, 
	ine varchar(50) NOT NULL, 
	cycle tinyint(2) NOT NULL, 
	code_composante varchar(10) NOT NULL DEFAULT '', 
	niveau_maitrise varchar(10) NOT NULL DEFAULT '', 
	periode INT(11) NOT NULL default '1', 
	login_saisie varchar(50) NOT NULL DEFAULT '', 
	date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
	PRIMARY KEY (id), INDEX ine_cycle_id_composante_periode (ine, cycle, code_composante, periode), UNIQUE(ine, cycle, code_composante, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'socle_eleves_syntheses' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_syntheses'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE socle_eleves_syntheses (id int(11) NOT NULL auto_increment, 
	ine varchar(50) NOT NULL, 
	cycle tinyint(2) NOT NULL, 
	synthese TEXT, 
	login_saisie varchar(50) NOT NULL DEFAULT '', 
	date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
	PRIMARY KEY (id), INDEX ine_cycle (ine, cycle), UNIQUE(ine, cycle)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'j_groupes_enseignements_complement' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_groupes_enseignements_complement';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_groupes_enseignements_complement (
						id int(11) unsigned NOT NULL auto_increment, 
						id_groupe int(11) NOT NULL,
						code VARCHAR(50) NOT NULL,
						PRIMARY KEY id (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'socle_eleves_enseignements_complements' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_enseignements_complements'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE socle_eleves_enseignements_complements (id int(11) NOT NULL auto_increment, 
	ine varchar(50) NOT NULL, 
	id_groupe INT(11) NOT NULL, 
	positionnement varchar(10) NOT NULL DEFAULT '', 
	login_saisie varchar(50) NOT NULL DEFAULT '', 
	date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
	PRIMARY KEY (id), INDEX ine_id_groupe (ine, id_groupe), UNIQUE(ine, id_groupe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'j_groupes_aid' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_groupes_aid'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_groupes_aid (id_groupe INT(11) NOT NULL default '0', 
		id_aid INT(11) NOT NULL default '0', 
		indice_aid INT(11) NOT NULL default '0', 
		etat varchar(255) NOT NULL default '', 
		PRIMARY KEY  (id_groupe, id_aid), INDEX id_groupe_id_aid (id_groupe, id_aid)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$test = sql_query1("SHOW TABLES LIKE 'modalites_accompagnement'");
if ($test != -1) {
	$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM modalites_accompagnement LIKE 'avec_commentaire';"));
	if ($test_champ==0) {
		$result .= "<br />";
		$result .= "<strong>Suppression de la table 'modalites_accompagnement'&nbsp;:</strong> ";
		$result_inter = traite_requete("DROP TABLE modalites_accompagnement;");
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	}
}

$result .= "<br /><strong>Ajout d'une table 'modalites_accompagnement'&nbsp;:</strong> ";
$test = sql_query1("SHOW TABLES LIKE 'modalites_accompagnement'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modalites_accompagnement (code VARCHAR(10) DEFAULT '', 
		libelle varchar(255) NOT NULL default '', 
		avec_commentaire char(1) NOT NULL default 'n', 
		PRIMARY KEY (code)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");

		$tab_modalite_accompagnement["PAP"]="Plan d’accompagnement personnalisé";
		$tab_modalite_accompagnement["PAI"]="Projet d’accueil individualisé";
		$tab_modalite_accompagnement["PPRE"]="Programme personnalisé de réussite éducative";
		$tab_modalite_accompagnement["PPS"]="Projet personnalisé de scolarisation";
		$tab_modalite_accompagnement["ULIS"]="Unité localisée pour l’inclusion scolaire";
		$tab_modalite_accompagnement["UPE2A"]="Unité pédagogique pour élèves allophones arrivants";
		$tab_modalite_accompagnement["SEGPA"]="Section d’enseignement général adapté";

		$tab_modalite_accompagnement_commentaire["PPRE"]="y";

		foreach($tab_modalite_accompagnement as $code => $libelle) {
			$ajout_sql="";
			if(isset($tab_modalite_accompagnement_commentaire[$code])) {
				$ajout_sql=", avec_commentaire='y'";
			}
			$result .= "&nbsp;-> Ajout de la modalité \"$code\" pour \"$libelle\"&nbsp;: ";
			$sql="INSERT INTO modalites_accompagnement SET code='".$code."', libelle='".addslashes($libelle)."'".$ajout_sql.";";
			$result_inter = traite_requete($sql);
			if ($result_inter == '') {
				$result .= msj_ok("SUCCES !");
			}
			else {
				$result .= msj_erreur("ECHEC !");
			}
			//$result .= "<br />";
		}
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$test = sql_query1("SHOW TABLES LIKE 'j_modalite_accompagnement_eleve'");
if ($test != -1) {
	$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM j_modalite_accompagnement_eleve LIKE 'periode';"));
	if ($test_champ==0) {
		$result .= "<br />";
		$result .= "<strong>Suppression de la table 'j_modalite_accompagnement_eleve'&nbsp;:</strong> ";
		$result_inter = traite_requete("DROP TABLE j_modalite_accompagnement_eleve;");
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	}
}

//$result .= "<br />";
$result .= "<strong>Ajout d'une table 'j_modalite_accompagnement_eleve'&nbsp;:</strong> ";
$test = sql_query1("SHOW TABLES LIKE 'j_modalite_accompagnement_eleve'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_modalite_accompagnement_eleve (code VARCHAR(10) DEFAULT '', 
		id_eleve INT(11) NOT NULL default '0', 
		periode INT(11) NOT NULL default '0', 
		commentaire TEXT, 
		PRIMARY KEY code_id_eleve_periode (code, id_eleve, periode), INDEX code_id_eleve_periode (code, id_eleve, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}


