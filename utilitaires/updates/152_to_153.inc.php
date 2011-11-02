<?php
/**
 * Fichier de mise à jour de la version 1.5.2 à la version 1.5.3
 * $Id: 152_to_153.inc.php 7930 2011-08-23 17:05:35Z dblanqui $
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
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.5.3" . $rc . $beta . " :</h3>";


$result .= "&nbsp;->Extension à 255 caractères du champ 'SESSION_ID' de la table 'log'<br />";
$query = mysql_query("ALTER TABLE `log` CHANGE `SESSION_ID` `SESSION_ID` VARCHAR( 255 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}

//===================================================

// Module examens blancs
$test = sql_query1("SHOW TABLES LIKE 'ex_examens'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_examens'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_examens (id int(11) unsigned NOT NULL auto_increment,
		intitule VARCHAR( 255 ) NOT NULL ,description TEXT NOT NULL ,
		date DATE NOT NULL default '0000-00-00',
		etat VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( id ));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_examens': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_matieres'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_matieres'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_matieres (
		id int(11) unsigned NOT NULL auto_increment,
		id_exam int(11) unsigned NOT NULL,
		matiere VARCHAR( 255 ) NOT NULL ,
		coef DECIMAL(3,1) NOT NULL default '1.0',
		bonus CHAR(1) NOT NULL DEFAULT 'n',
		ordre INT(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_matieres': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_classes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_classes'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_classes (
		id int(11) unsigned NOT NULL auto_increment,
		id_exam int(11) unsigned NOT NULL,
		id_classe int(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_classes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_groupes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_groupes'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_groupes (
		id int(11) unsigned NOT NULL auto_increment,
		id_exam int(11) unsigned NOT NULL,
		matiere varchar(50) NOT NULL,
		id_groupe int(11) unsigned NOT NULL,
		type VARCHAR( 255 ) NOT NULL ,
		id_dev int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_groupes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_notes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_notes'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_notes (
		id int(11) unsigned NOT NULL auto_increment,
		id_ex_grp int(11) unsigned NOT NULL,
		login VARCHAR(255) NOT NULL default '',
		note float(10,1) NOT NULL default '0.0',
		statut varchar(4) NOT NULL default '',
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_notes': ".$result_inter."<br />";
	}
}

//===================================================

// Module examens blancs

$test = sql_query1("SHOW TABLES LIKE 'eb_epreuves'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_epreuves'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_epreuves (
		id int(11) unsigned NOT NULL auto_increment,
		intitule VARCHAR( 255 ) NOT NULL ,
		description TEXT NOT NULL ,
		type_anonymat VARCHAR( 255 ) NOT NULL ,
		date DATE NOT NULL default '0000-00-00',
		etat VARCHAR( 255 ) NOT NULL ,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_examens': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_copies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_copies'. ";
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
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_copies': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_salles'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_salles'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_salles (
		id int(11) unsigned NOT NULL auto_increment,
		salle VARCHAR( 255 ) NOT NULL ,
		id_epreuve int(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_salles': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_groupes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_groupes'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_groupes (
		id int(11) unsigned NOT NULL auto_increment,
		id_epreuve int(11) unsigned NOT NULL,
		id_groupe int(11) unsigned NOT NULL,
		transfert varchar(1) NOT NULL DEFAULT 'n',
		PRIMARY KEY ( id )
		);";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_groupes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_profs'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_profs'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_profs (
		id int(11) unsigned NOT NULL auto_increment,
		id_epreuve int(11) unsigned NOT NULL,
		login_prof VARCHAR(255) NOT NULL default '',
		PRIMARY KEY ( id )
		);";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_profs': ".$result_inter."<br />";
	}
}


$test = sql_query1("SHOW TABLES LIKE 'synthese_app_classe'");
if ($test == -1) {
	$result .= "<br />Création de la table 'synthese_app_classe'. ";
	$sql="CREATE TABLE IF NOT EXISTS synthese_app_classe (
		id_classe int(11) NOT NULL default '0',
		periode int(11) NOT NULL default '0',
		synthese text NOT NULL,
		PRIMARY KEY  (id_classe,periode)
		);";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'synthese_app_classe': ".$result_inter."<br />";
	}
}



$test = sql_query1("SHOW TABLES LIKE 'message_login'");
if ($test == -1) {
	$result .= "<br />Création de la table 'message_login'. ";
	$sql="CREATE TABLE IF NOT EXISTS message_login (
		id int(11) NOT NULL auto_increment,
		texte text NOT NULL,PRIMARY KEY  (id)
		);";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'message_login': ".$result_inter."<br />";
	}
}


$test = mysql_query("SELECT 1=1 FROM message_login;");
if (mysql_num_rows($test)==0) {
	$result .= "<br />Insertion d'un message de login: ";
	$sql="INSERT INTO message_login SET texte='Espace pour un message en page de login paramétrable en Gestion des connexions.';";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur lors de l'insertion: ".$result_inter."<br />";
	}
	else {
		$result.=msj_ok();

		$id_tmp=mysql_insert_id();
		$sql="SELECT 1=1 FROM setting WHERE name='message_login';";
		$test=mysql_query($sql);
		if (mysql_num_rows($test)==0) {
			$result .= "Insertion de l'indice du message de login: ";

			$sql="INSERT INTO setting SET name='message_login', value='$id_tmp';";
			$result_inter=traite_requete($sql);
			if ($result_inter != '') {
				$result .= "<br />Erreur lors de l'insertion de l'indice du message de login à afficher: ".$result_inter."<br />";
			}
			else {
				$result.=msj_ok();
			}
		}
		else {
			// Ca ne devrait pas arriver
			$result .= "<br />Mise à jour de l'indice du message de login: ";
			$sql="UPDATE setting SET value='$id_tmp' WHERE name='message_login';";
			$result_inter=traite_requete($sql);
			if ($result_inter != '') {
				$result .= "<br />Erreur lors de la mise à jour de l'indice du message de login à afficher: ".$result_inter."<br />";
			}
			else {
				$result.=msj_ok();
			}
		}
	}
	$result.="<br />";
}

$result .= "&nbsp;->Ajout d'un champ date_decompte à la table 'messages'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM messages LIKE 'date_decompte';"));
if ($test_date_decompte>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE messages ADD date_decompte INT NOT NULL DEFAULT '0';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}


// Ajout d'une table pays
$test = sql_query1("SHOW TABLES LIKE 'pays'");
if ($test == -1) {
	$result .= "<br />Création de la table 'pays'. ";
	$sql="CREATE TABLE IF NOT EXISTS pays (code_pays VARCHAR( 50 ) NOT NULL, nom_pays VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( code_pays ));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur lors de la création de la table 'pays': ".$result_inter."<br />";
	}
	else {
		$result .= msj_ok();
	}
}

$test=mysql_num_rows(mysql_query("SHOW COLUMNS FROM pays LIKE 'nom_court';"));
if($test>0) {
	$result .= "<br />Destruction de la table 'pays' mal formatée.";
	$sql="DROP TABLE pays;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur lors de la destruction de la table 'pays': ".$result_inter."<br />";
	}
	else {
		$result .= msj_ok();

		$result .= "<br />Re-création de la table 'pays'.";
		$sql="CREATE TABLE IF NOT EXISTS pays (code_pays VARCHAR( 50 ) NOT NULL, nom_pays VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( code_pays ));";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= "<br />Erreur lors de la création de la table 'pays': ".$result_inter."<br />";
		}
		else {
			$result .= msj_ok();
		}
	}
}

//$result="";
$result.="&nbsp;->Ajout d'un champ 'valeur' à la table 'ex_groupes'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ex_groupes LIKE 'valeur';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE ex_groupes ADD valeur VARCHAR(255) NOT NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
	//echo $result;
}


//---------------
// Ajouts d'index

$result .= add_index('absences_rb','eleve_debut_fin_retard','eleve_id, debut_ts, fin_ts, retard_absence');
$result .= add_index('classes','classe','classe');
$result .= add_index('ct_entry','date_ct','date_ct');
$result .= add_index('ct_entry','id_date_heure','id_groupe, date_ct, heure_entry');
$result .= add_index('ct_devoirs_entry','groupe_date','id_groupe, date_ct');
$result .= add_index('cn_devoirs','conteneur_date','`id_conteneur`, `date`');
$result .= add_index('cn_cahier_notes','groupe_periode','`id_groupe`, `periode`');
$result .= add_index('cn_conteneurs','parent_racine','`parent`, `id_racine`');
$result .= add_index('cn_conteneurs','racine_bulletin','`id_racine`, `display_bulletin`');
$result .= add_index('cn_notes_devoirs','devoir_statut','`id_devoir`, `statut`');
$result .= add_index('groupes','id_name','`id`, `name`');
$result .= add_index('j_eleves_professeurs','classe_professeur','`id_classe`, `professeur`');
$result .= add_index('j_eleves_professeurs','professeur_classe','`professeur`, `id_classe`');
$result .= add_index('j_eleves_groupes','login','`login`');
$result .= add_index('j_eleves_classes','login_periode','`login`,`periode`');
$result .= add_index('j_groupes_classes','id_classe_coef','`id_classe`,`coef`');
$result .= add_index('j_groupes_classes','saisie_ects_id_groupe','`saisie_ects`,`id_groupe`');
$result .= add_index('j_groupes_professeurs','login','`login`');
$result .= add_index('log','start_time','`START`');
$result .= add_index('log','end_time','`END`');
$result .= add_index('log','login_session_start','`LOGIN`,`SESSION_ID`,`START`');
$result .= add_index('matieres_notes','groupe_periode_statut','`id_groupe`,`periode`,`statut`');
$result .= add_index('matieres_appreciations_tempo','groupe_periode','`id_groupe`,`periode`');
$result .= add_index('messages','date_debut_fin','`date_debut`,`date_fin`');
$result .= add_index('preferences','login_name','`login`,`name`');
$result .= add_index('periodes','id_classe','`id_classe`');


//--------------------
// Admissions Post-Bac

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'active_mod_apb'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('active_mod_apb', 'n');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre active_mod_apb à 'n': Ok !");
  } else {
    $result.=msj_erreur(": Définition du paramètre active_mod_apb à 'n'Erreur !");
  }
} else {
  $result .= msj_present("Le paramètre active_mod_apb existe déjà dans la table setting.");
}

$result .= "&nbsp;->Ajout d'un champ apb_niveau à la table 'classes'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'apb_niveau';"));
if ($test_date_decompte>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE classes ADD apb_niveau VARCHAR(15) NOT NULL DEFAULT '';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}

$result .= "&nbsp;->Ajout d'un champ apb_langue_vivante à la table 'j_groupes_classes'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM j_groupes_classes LIKE 'apb_langue_vivante';"));
if ($test_date_decompte>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE j_groupes_classes ADD apb_langue_vivante VARCHAR(3) NOT NULL DEFAULT '';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}

//--------------------
// Signalements

$test = sql_query1("SHOW TABLES LIKE 'j_signalement'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_signalement'. ";
	$sql="CREATE TABLE IF NOT EXISTS j_signalement (id_groupe int(11) NOT NULL default '0',
			login varchar(50) NOT NULL default '',
			periode int(11) NOT NULL default '0',
			nature varchar(50) NOT NULL default '',
			valeur varchar(50) NOT NULL default '',
			declarant varchar(50) NOT NULL default '',
			PRIMARY KEY (id_groupe,login,periode,nature), INDEX (login));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur lors de la création de la table 'j_signalement': ".$result_inter."<br />";
	}
	else {
		$result .= msj_ok();
	}
}


// mod_discipline

$test = sql_query1("SHOW TABLES LIKE 's_alerte_mail'");
if ($test == -1) {
	$result .= "<br />Création de la table 's_alerte_mail'. ";
	$sql="CREATE TABLE IF NOT EXISTS s_alerte_mail (id int(11) unsigned NOT NULL auto_increment, id_classe smallint(6) unsigned NOT NULL, destinataire varchar(50) NOT NULL default '', PRIMARY KEY (id), INDEX (id_classe,destinataire));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur lors de la création de la table 's_alerte_mail': ".$result_inter."<br />";
	}
	else {
		$result .= msj_ok();
	}
}


$result .= "&nbsp;->Ajout d'un champ message_id à la table 's_incidents'<br />";
$test=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_incidents LIKE 'message_id';"));
if ($test>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE s_incidents ADD message_id VARCHAR(50) NOT NULL DEFAULT '';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}


// ============= Suppression de tables inutiles pour EDT2

$sql = "SHOW TABLES LIKE 'edt_gr_nom'";
$req_existence = mysql_query($sql);
if (mysql_num_rows($req_existence) != 0) {
    $sql = "DROP TABLE edt_gr_nom";
    $req_deletion = mysql_query($sql);
    if ($req_deletion) {
        $result .= "<p style=\"color:green;\">Suppression de la table <strong>edt_gr_nom</strong> : ok</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Suppression de la table <strong>edt_gr_nom</strong> : Erreur</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Suppression de la table <strong>edt_gr_nom</strong> : déjà réalisée.</p>";
}

$sql = "SHOW TABLES LIKE 'edt_gr_profs'";
$req_existence = mysql_query($sql);
if (mysql_num_rows($req_existence) != 0) {
    $sql = "DROP TABLE edt_gr_profs";
    $req_deletion = mysql_query($sql);
    if ($req_deletion) {
        $result .= "<p style=\"color:green;\">Suppression de la table <strong>edt_gr_profs</strong> : ok</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Suppression de la table <strong>edt_gr_profs</strong> : Erreur</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Suppression de la table <strong>edt_gr_profs</strong> : déjà réalisée.</p>";
}

$sql = "SHOW TABLES LIKE 'edt_gr_classes'";
$req_existence = mysql_query($sql);
if (mysql_num_rows($req_existence) != 0) {
    $sql = "DROP TABLE edt_gr_classes";
    $req_deletion = mysql_query($sql);
    if ($req_deletion) {
        $result .= "<p style=\"color:green;\">Suppression de la table <strong>edt_gr_classes</strong> : ok</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Suppression de la table <strong>edt_gr_classes</strong> : Erreur</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Suppression de la table <strong>edt_gr_classes</strong> : déjà réalisée.</p>";
}

$sql = "SHOW TABLES LIKE 'edt_gr_eleves'";
$req_existence = mysql_query($sql);
if (mysql_num_rows($req_existence) != 0) {
    $sql = "DROP TABLE edt_gr_eleves";
    $req_deletion = mysql_query($sql);
    if ($req_deletion) {
        $result .= "<p style=\"color:green;\">Suppression de la table <strong>edt_gr_eleves</strong> : ok</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Suppression de la table <strong>edt_gr_eleves</strong> : Erreur</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Suppression de la table <strong>edt_gr_eleves</strong> : déjà réalisée.</p>";
}

// ============= Renommage d'une table pour EDT2

$sql = "RENAME TABLE absences_creneaux TO edt_creneaux";
$req_rename = mysql_query($sql);
if ($req_rename){
    $result .= "<p style=\"color:green;\">Renommage de la table <strong>absences_creneaux</strong> en <strong>edt_creneaux</strong> : ok.</p>";
}
else {
    $result .= "<p style=\"color:blue;\">Renommage de la table <strong>absences_creneaux</strong> en <strong>edt_creneaux</strong> : déjà réalisé.</p>";
}

// ============= Renommage d'une table pour EDT2

$sql = "RENAME TABLE absences_creneaux_bis TO edt_creneaux_bis";
$req_rename = mysql_query($sql);
if ($req_rename){
    $result .= "<p style=\"color:green;\">Renommage de la table <strong>absences_creneaux_bis</strong> en <strong>edt_creneaux_bis</strong> : ok.</p>";
}
else {
    $result .= "<p style=\"color:blue;\">Renommage de la table <strong>absences_creneaux_bis</strong> en <strong>edt_creneaux_bis</strong> : déjà réalisé.</p>";
}
$result .= add_index('edt_creneaux','heures_debut_fin','heuredebut_definie_periode, heurefin_definie_periode');

// ============= Insertion d'un champ pour EDT2

$sql = "SELECT jour_creneau FROM edt_creneaux LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE edt_creneaux ADD jour_creneau VARCHAR(20)";
    $req_add_rank = mysql_query($sql_request);
    if ($req_add_rank) {
        $result .= "<p style=\"color:green;\">Ajout du champ jour_creneau dans la table <strong>edt_creneaux</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ jour_creneau à la table <strong>edt_creneaux</strong> : Erreur.</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ jour_creneau à la table <strong>edt_creneaux</strong> : déjà réalisé.</p>";

}

// Sur certaines bases il est arrivé pour une raison inconnue que le champ type_creneaux soit manquant
$result .= "&nbsp;->Ajout si nécessaire d'un champ 'type_creneaux' à la table 'edt_creneaux'<br />";
$test_type_creneaux=mysql_num_rows(mysql_query("SHOW COLUMNS FROM edt_creneaux LIKE 'type_creneaux';"));
if ($test_type_creneaux>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE edt_creneaux ADD type_creneaux VARCHAR( 15 ) NOT NULL AFTER suivi_definie_periode;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}

// ============= Insertion d'un champ pour EDT2

$sql = "SELECT id_aid FROM edt_cours LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE edt_cours ADD id_aid CHAR(10) DEFAULT '' ";
    $req_add_rank = mysql_query($sql_request);
    if ($req_add_rank) {
        $result .= "<p style=\"color:green;\">Ajout du champ id_aid dans la table <strong>edt_cours</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ id_aid à la table <strong>edt_cours</strong> : Erreur.</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ id_aid à la table <strong>edt_cours</strong> : déjà réalisé.</p>";
}

// ============== vérification du TYPE du champ id_aid et si c'est un INT, le changer en CHAR

// procédure utile pour ceux qui ont utilisé trunk en production
// alors que le champ id_aid (dans sa première version) était défini à tort comme un INT

$sql = "SELECT * FROM edt_cours LIMIT 1";
$req = mysql_query($sql);
$modif = false;
if ($req) {
    $i = 0;
    while ($i < mysql_num_fields($req)) {
        $meta = mysql_fetch_field($req,$i);
        //echo "<p>".$meta->type."</p>";
        if ($meta->name == "id_aid") {
            if ($meta->type == "int") {
                $modif = true;
            }
        }
        $i++;
    }
}
if ($modif == true) {
    $sql = "ALTER TABLE edt_cours MODIFY id_aid CHAR(10)";
    $req = mysql_query($sql);
    if ($req) {
        $result .= "<p style=\"color:green;\">Changement du type du champ id_aid dans la table <strong>edt_cours</strong> : ok.</p>";
    }

    // ============== Faire la mise à niveau du champ id_aid s'il a changé de type
    $sql = "SELECT id_cours, id_groupe, id_aid FROM edt_cours";
    $req = mysql_query($sql);
    if ($req) {
        while ($rep = mysql_fetch_array($req)) {
            if (($rep['id_groupe'] != "") AND ($rep['id_aid'] == "0")) {
                $sql = "UPDATE edt_cours SET id_aid = '' WHERE id_cours = '".$rep['id_cours']."' ";
                mysql_query($sql);
            }
        }
    }

}


// ============= Mise à niveau de la table edt_cours
$nb_changes = 0;
$sql = "SELECT id_groupe, id_cours FROM edt_cours";
$req_group = mysql_query($sql);
if ($req_group) {
    while ($rep_group = mysql_fetch_array($req_group)) {
        $analyse = explode("|", $rep_group['id_groupe']);
        if ($analyse[0] == "AID") {
            $sql = "UPDATE edt_cours SET id_aid = '".$analyse[1]."', id_groupe = '' WHERE id_cours = '".$rep_group['id_cours']."' ";
            $req = mysql_query($sql);
            $nb_changes++;
        }
    }
}
if ($nb_changes != 0) {
    $result .= "<p style=\"color:green;\">".$nb_changes." champs dans la table edt_cours ont été mis à niveau.</p>";
}
else {
    $result .= "<p style=\"color:blue;\">La table edt_cours est à niveau.</p>";
}


// ============= Mise à jour de la table edt_semaines
$changes = false;
$sql = "SELECT id_edt_semaine FROM edt_semaines";
$req = mysql_query($sql);
if ($req) {
    if (mysql_num_rows($req) == 52) {
        $sql = "INSERT INTO edt_semaines SET id_edt_semaine = '53', num_edt_semaine = '53', type_edt_semaine = '', num_semaines_etab = '0' ";
        $req_insert = mysql_query($sql);
        if ($req_insert) {
            $changes = true;
        }
    }
}
if ($changes) {
    $result .= "<p style=\"color:green;\">Ajout d'un 53eme enregistrement dans la table edt_semaine : ok.</p>";
}
else {
    $result .= "<p style=\"color:blue;\">La table edt_semaines contient bien 53 enregistrements.</p>";
}


// ==== Harmonisation de la taille de clés étrangères

$result .= "&nbsp;->Extension à 255 caractères du champ 'id_matiere' de la table 'j_groupes_matieres'<br />";
$query = mysql_query("ALTER TABLE `j_groupes_matieres` CHANGE `id_matiere` `id_matiere` VARCHAR( 255 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}


$result .= "&nbsp;->Passage du champ 'id' de la table 'classes' en type Integer(11)<br />";
$query = mysql_query("ALTER TABLE `classes` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}


// Paramètre d'activation de la synchro à la volée Scribe NG

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'sso_scribe'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('sso_scribe', 'no');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre sso_scribe à 'no': Ok !");
  } else {
    $result.=msj_erreur(": Définition du paramètre sso_scribe à 'no'Erreur !");
  }
} else {
  $result .= msj_present("Le paramètre sso_scribe existe déjà dans la table setting.");
}


// Modification du type des champs id_mat pour pouvoir dépasser 127

$result .= "&nbsp;->Modification de 'id_mat' de TINYINT en INT dans la table 'notanet'<br />";
$query = mysql_query("ALTER TABLE notanet CHANGE id_mat id_mat INT( 4 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}

$result .= "&nbsp;->Modification de 'id_mat' de TINYINT en INT dans la table 'notanet_corresp'<br />";
$query = mysql_query("ALTER TABLE notanet_corresp CHANGE id_mat id_mat INT( 4 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}

$result .= "&nbsp;->Modification de 'id_mat' de TINYINT en INT dans la table 'notanet_app'<br />";
$query = mysql_query("ALTER TABLE notanet_app CHANGE id_mat id_mat INT( 4 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}

// Modification du type des champs id_classe pour pouvoir dépasser 127
$result .= "&nbsp;->Modification de 'id_classe' de TINYINT en SMALLINT dans la table 'notanet_verrou'<br />";
$query = mysql_query("ALTER TABLE notanet_verrou CHANGE id_classe id_classe SMALLINT( 6 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}


// Ajout d'une colonne sur les ECTS, pour permettre une présaisie par le prof
$result .= "<br />Modification de la table 'ects_credits' (ajout de la colonne 'mention_prof').<br />";
$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ects_credits LIKE 'mention_prof'"));
if ($test == 0) {
  $query = mysql_query("ALTER TABLE `ects_credits` ADD `mention_prof` VARCHAR(255) AFTER `mention` ;");
  if ($query) {
    $result .= msj_ok();
  } else {
    $result .= msj_erreur( );
  }
} else {
  $result .= msj_present("La colonne 'mention_prof' existe deja.");
}

$result .= "<br />Modification de la table 'ects_credits' (changements sur la colonne 'valeur').<br />";
$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ects_credits LIKE 'valeur'"));
if ($test == 1) {
  $query = mysql_query("ALTER TABLE `ects_credits` CHANGE `valeur` `valeur` VARCHAR(255);");
  if ($query) {
    $result .= msj_ok();
  } else {
    $result .= msj_erreur( );
  }
} else {
  $result .= msj_erreur("La colonne 'valeur' n'existe pas, modification impossible.");
}

$result .= "<br />Modification de la table 'ects_credits' (changements sur la colonne 'mention').<br />";
$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM ects_credits LIKE 'mention'"));
if ($test == 1) {
  $query = mysql_query("ALTER TABLE `ects_credits` CHANGE `mention` `mention` VARCHAR(255);");
  if ($query) {
    $result .= msj_ok();
  } else {
    $result .= msj_erreur( );
  }
} else {
  $result .= msj_erreur("La colonne 'mention' n'existe pas, modification impossible.");
}

$result .= "<br />Modification de la table 'j_groupes_classes' (changements sur la colonne 'valeur_ects').<br />";
$test = mysql_num_rows(mysql_query("SHOW COLUMNS FROM j_groupes_classes LIKE 'valeur_ects'"));
if ($test == 1) {
  $query = mysql_query("ALTER TABLE `j_groupes_classes` CHANGE `valeur_ects` `valeur_ects` INT(11) NULL;");
  if ($query) {
    $result .= msj_ok();
  } else {
    $result .= msj_erreur( );
  }
} else {
  $result .= msj_erreur("La colonne 'valeur_ects' n'existe pas, modification impossible.");
}


// Ajout d'un paramètre de droits d'accès à la pré-saisie des ECTS pour les profs
$req_test=mysql_query("SELECT value FROM setting WHERE name = 'GepiAccesSaisieEctsProf'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('GepiAccesSaisieEctsProf', 'no');");
  if ($result_inter == '') {
    $result.=msj_ok("Initialisation du paramètre GepiAccesSaisieEctsProf à 'n' : Ok");
  } else {
    $result.=msj_erreur(": Initialisation du paramètre GepiAccesSaisieEctsProf à 'n'");
  }
} else {
  $result .= msj_present("Le paramètre GepiAccesSaisieEctsProf existe déjà dans la table setting.");
}


// Ajout d'un paramètre de droits d'accès au tableau récapitulatif des ECTS pour les profs
$req_test=mysql_query("SELECT value FROM setting WHERE name = 'GepiAccesRecapitulatifEctsProf'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('GepiAccesRecapitulatifEctsProf', 'yes');");
  if ($result_inter == '') {
    $result.=msj_ok("Initialisation du paramètre GepiAccesRecapitulatifEctsProf à 'yes' : Ok");
  } else {
    $result.=msj_erreur(": Initialisation du paramètre GepiAccesRecapitulatifEctsProf à 'yes'");
  }
} else {
  $result .= msj_present("Le paramètre GepiAccesRecapitulatifEctsProf existe déjà dans la table setting.");
}

// Ajout d'un paramètre de droits d'accès au tableau récapitulatif des ECTS pour la scolarité
$req_test=mysql_query("SELECT value FROM setting WHERE name = 'GepiAccesRecapitulatifEctsScolarite'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('GepiAccesRecapitulatifEctsScolarite', 'yes');");
  if ($result_inter == '') {
    $result.=msj_ok("Initialisation du paramètre GepiAccesRecapitulatifEctsScolarite à 'yes' : Ok");
  } else {
    $result.=msj_erreur(": Initialisation du paramètre GepiAccesRecapitulatifEctsScolarite à 'yes'");
  }
} else {
  $result .= msj_present("Le paramètre GepiAccesRecapitulatifEctsScolarite existe déjà dans la table setting.");
}


// Ajout d'un champ autoriser_inscript_multiples à la table aid_config
$result .= "&nbsp;->Ajout d'un champ autoriser_inscript_multiples à la table 'aid_config'<br />";
$test_autoriser_inscript_multiples=mysql_num_rows(mysql_query("SHOW COLUMNS FROM aid_config LIKE 'autoriser_inscript_multiples';"));
if ($test_autoriser_inscript_multiples>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE aid_config ADD autoriser_inscript_multiples CHAR(1) DEFAULT 'n';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}

$result .= "<br />&nbsp;->Ajout de la table table matieres_app_corrections<br />";
$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'matieres_app_corrections'"));
if ($test == 0) {
	$result_inter = traite_requete("CREATE TABLE matieres_app_corrections (login varchar(255) NOT NULL default '', id_groupe int(11) NOT NULL default '0', periode int(11) NOT NULL default '0', appreciation text NOT NULL, PRIMARY KEY (login,id_groupe,periode));");
	if ($result_inter == '') {
		$result .= msj_ok("La table matieres_app_corrections a été créée !");
	}
	else {
		$result .= $result_inter."<br />";
	}
} else {
	$result .= msj_present("La table matieres_app_corrections existe déjà.");
}

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'autoriser_correction_bulletin'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('autoriser_correction_bulletin', 'y');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre 'autoriser_correction_bulletin' à 'y' : Ok");
  } else {
    $result.=msj_erreur(": Définition du paramètre 'autoriser_correction_bulletin' à 'y'");
  }
} else {
  $result .= msj_present("Le paramètre 'autoriser_correction_bulletin' existe déjà dans la table 'setting'.");
}




$test = sql_query1("SHOW TABLES LIKE 'gc_projets'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_projets'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_projets (
		id smallint(6) unsigned NOT NULL auto_increment,
		projet VARCHAR( 255 ) NOT NULL ,
		commentaire TEXT NOT NULL ,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_projets': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'gc_divisions'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_divisions'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_divisions (
		id int(11) unsigned NOT NULL auto_increment,
		projet VARCHAR( 255 ) NOT NULL ,
		id_classe smallint(6) unsigned NOT NULL,
		classe varchar(100) NOT NULL default '',
		statut enum( 'actuelle', 'future', 'red', 'arriv' ) NOT NULL DEFAULT 'future',
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_divisions': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'gc_options'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_options'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_options (
		id int(11) unsigned NOT NULL auto_increment,
		projet VARCHAR( 255 ) NOT NULL ,
		opt VARCHAR( 255 ) NOT NULL ,
		type ENUM('lv1','lv2','lv3','autre') NOT NULL ,
		obligatoire ENUM('o','n') NOT NULL ,
		exclusive smallint(6) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_options': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'gc_options_classes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_options_classes'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_options_classes (
		id int(11) unsigned NOT NULL auto_increment,
		projet VARCHAR( 255 ) NOT NULL ,
		opt_exclue VARCHAR( 255 ) NOT NULL ,
		classe_future VARCHAR( 255 ) NOT NULL ,
		commentaire TEXT NOT NULL ,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_options_classes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'gc_ele_arriv_red'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_ele_arriv_red'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_ele_arriv_red (
		login VARCHAR( 255 ) NOT NULL,
		statut ENUM('Arriv','Red') NOT NULL ,
		projet VARCHAR( 255 ) NOT NULL ,
		PRIMARY KEY ( login , projet )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_ele_arriv_red': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'gc_affichages'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_affichages'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_affichages (
		id int(11) unsigned NOT NULL auto_increment,
		id_aff int(11) unsigned NOT NULL,
		id_req int(11) unsigned NOT NULL,
		projet VARCHAR( 255 ) NOT NULL ,
		type VARCHAR(255) NOT NULL,
		valeur varchar(255) NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_affichages': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'gc_eleves_options'");
if ($test == -1) {
	$result .= "<br />Création de la table 'gc_eleves_options'. ";
	$sql="CREATE TABLE IF NOT EXISTS gc_eleves_options (
		id int(11) unsigned NOT NULL auto_increment,
		login VARCHAR( 255 ) NOT NULL ,
		profil enum('GC','C','RAS','B','TB') NOT NULL default 'RAS',
		moy VARCHAR( 255 ) NOT NULL ,
		nb_absences VARCHAR( 255 ) NOT NULL ,
		non_justifie VARCHAR( 255 ) NOT NULL ,
		nb_retards VARCHAR( 255 ) NOT NULL ,
		projet VARCHAR( 255 ) NOT NULL ,
		id_classe_actuelle VARCHAR(255) NOT NULL ,
		classe_future VARCHAR(255) NOT NULL ,
		liste_opt VARCHAR( 255 ) NOT NULL ,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'gc_eleves_options': ".$result_inter."<br />";
	}
}

$result .= "<br />&nbsp;->Contrôle de la clé primaire de 'gc_ele_arriv_red'<br />";
$req_test=mysql_query("SHOW INDEXES FROM gc_ele_arriv_red WHERE Key_name='PRIMARY';");
$res_test=mysql_num_rows($req_test);
if ($res_test<2){
  $query=mysql_query("ALTER TABLE gc_ele_arriv_red DROP PRIMARY KEY;");
  if ($query) {
    $result.=msj_ok("Suppression de la clé primaire sur 'login' dans 'gc_ele_arriv_red' : Ok");
    $query=mysql_query("ALTER TABLE gc_ele_arriv_red ADD PRIMARY KEY ( login , projet );");
    if ($query) {
      $result.=msj_ok("Définition de la clé primaire sur 'login' et 'projet' dans 'gc_ele_arriv_red' : Ok !");
    } else {
      $result.=msj_erreur(": Echec de la définition de la clé primaire sur 'login' et 'projet' dans 'gc_ele_arriv_red'");
    }
  } else {
    $result.=msj_erreur(": Suppression de la clé primaire sur 'login' dans 'gc_ele_arriv_red'");
  }
} else {
  $result .= msj_present("La clé primaire de 'gc_ele_arriv_red' est déjà sur $res_test champs.");
}

$result .= "<br />&nbsp;->Extension à 255 caractères du champ 'USER_AGENT' de la table 'log'<br />";
$query = mysql_query("ALTER TABLE log CHANGE USER_AGENT USER_AGENT VARCHAR( 255 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur( );
}
$result .= "<br />";

$test = sql_query1("SHOW TABLES LIKE 's_categories'");
if ($test == -1) {
	$result .= "<br />Création de la table 's_categories'. ";
	$sql="CREATE TABLE IF NOT EXISTS s_categories ( id INT(11) NOT NULL
		auto_increment, categorie varchar(50) NOT NULL
		default '',sigle varchar(20) NOT NULL
		default '', PRIMARY KEY (id) )
		ENGINE=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 's_categories': ".$result_inter."<br />";
	}
}

$result .= "&nbsp;->Ajout d'un champ 'id_categorie' à la table 's_incidents'<br />";
$test=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_incidents LIKE 'id_categorie';"));
if ($test>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE s_incidents ADD id_categorie INT(11) AFTER nature;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur( );
	}
}

// Ajout Eric modif notification par mail dans le module discipline
$result .= "&nbsp;->Ajout d'un champ 'adresse' à la table 's_alerte_mail'<br />";
$test=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_alerte_mail LIKE 'adresse';"));
if ($test>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE `s_alerte_mail` ADD `adresse` VARCHAR( 250 ) NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}


// ============= Insertion d'un champ pour abs2
$sql = "SELECT date_fin FROM periodes LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE periodes ADD date_fin TIMESTAMP";
    $req_add_rank = mysql_query($sql_request);
    if ($req_add_rank) {
        $result .= "<p style=\"color:green;\">Ajout du champ date_fin dans la table <strong>periodes</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ date_fin à la table <strong>periodes</strong> : Erreur.</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ date_fin à la table <strong>periodes</strong> : déjà réalisé.</p>";

}



$result .= "<br /><br /><strong>Mise à jour du module mod_abs2 :</strong><br />";
$result .= "&nbsp;->Ajout des tables absence 2<br />";

#-----------------------------------------------------------------------------
#-- a_motifs
#-----------------------------------------------------------------------------
$test = sql_query1("SHOW TABLES LIKE 'a_motifs'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_motifs'. ";
	$sql="
CREATE TABLE a_motifs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du motif',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)ENGINE=MyISAM COMMENT='Liste des motifs possibles pour une absence';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_motifs': ".$result_inter."<br />";
	}

	$sql="
INSERT INTO `a_motifs` (`id`, `nom`, `commentaire`, `sortable_rank`) VALUES (1, 'Médical', 'L''élève est absent pour raison médicale', 1),(2, 'Familial', 'L''élève est absent pour raison familiale', 2),(3, 'Sportive', 'L''élève est absent pour cause de competition sportive', 3);
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création des motifs par défaut : ".$result_inter."<br />";
	}
}

#-----------------------------------------------------------------------------
#-- a_justifications
#-----------------------------------------------------------------------------
$test = sql_query1("SHOW TABLES LIKE 'a_justifications'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_justifications'. ";
	$sql="
CREATE TABLE a_justifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de la justification',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)ENGINE=MyISAM COMMENT='Liste des justifications possibles pour une absence';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_justifications': ".$result_inter."<br />";
	}

	$sql="
INSERT INTO `a_justifications` (`id`, `nom`, `commentaire`, `sortable_rank`) VALUES (1, 'Certificat médical', 'Une justification établie par une autorité médicale', 1),(2, 'Courrier familial', 'Justification par courrier de la famille', 2),(3, 'Justificatif d''une administration publique', 'Justification émise par une administration publique', 3);
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création des justifications par défaut : ".$result_inter."<br />";
	}
}

#-----------------------------------------------------------------------------
#-- a_types
#-----------------------------------------------------------------------------
$test = sql_query1("SHOW TABLES LIKE 'a_types'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_types'. ";
	$sql="
CREATE TABLE a_types
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du type d\'absence',
	justification_exigible TINYINT COMMENT 'Ce type d\'absence doit entrainer une justification de la part de la famille',
	sous_responsabilite_etablissement VARCHAR(255) default 'NON_PRECISE' COMMENT 'L\'eleve est sous la responsabilite de l\'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l\'eleve est encore sous la responsabilité de l\'etablissement. Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	manquement_obligation_presence VARCHAR(50) default 'NON_PRECISE' COMMENT 'L\'eleve manque à ses obligations de presence (L\'absence apparait sur le bulletin). Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	retard_bulletin VARCHAR(50) default 'NON_PRECISE' COMMENT 'La saisie est comptabilisée dans le bulletin en tant que retard. Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	type_saisie VARCHAR(50) default 'NON_PRECISE' COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)ENGINE=MyISAM COMMENT='Liste des types d\'absences possibles dans l\'etablissement';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_types': ".$result_inter."<br />";
	}

	$sql="DROP TABLE IF EXISTS a_types_statut;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur lors de la destruction de la table 'a_types_statut': ".$result_inter."<br />";
	}

	$result .= "<br />Création de la table 'a_types_statut'. ";
	$sql="
CREATE TABLE a_types_statut
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	id_a_type INTEGER(11)  NOT NULL COMMENT 'Cle etrangere de la table a_type',
	statut VARCHAR(20)  NOT NULL COMMENT 'Statut de l\'utilisateur',
	PRIMARY KEY (id),
	INDEX a_types_statut_FI_1 (id_a_type),
	CONSTRAINT a_types_statut_FK_1
		FOREIGN KEY (id_a_type)
		REFERENCES a_types (id)
		ON DELETE CASCADE
)ENGINE=MyISAM COMMENT='Liste des statuts autorises à saisir des types d\'absences';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_types_statut': ".$result_inter."<br />";
	}

	$sql="
INSERT INTO `a_types` (`id`, `nom`, `justification_exigible`, `sous_responsabilite_etablissement`, `manquement_obligation_presence`, `retard_bulletin`, `type_saisie`, `commentaire`, `sortable_rank`) VALUES(1, 'Absence scolaire', 1, 'FAUX', 'VRAI', 'NON_PRECISE', 'NON_PRECISE', 'L''élève n''est pas présent pour suivre sa scolarité.', 1),(2, 'Retard intercours', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est en retard lors de l''intercours', 2),(3, 'Retard extérieur', 0, 'FAUX', 'VRAI', 'VRAI', 'NON_PRECISE', 'L''élève est en retard lors de son arrivée dans l''etablissement', 3),(4, 'Erreur de saisie', 0, 'NON_PRECISE', 'NON_PRECISE', 'NON_PRECISE', 'NON_PRECISE', 'Il y a probablement une erreur de saisie sur cet enregistrement.', 4),(5, 'Infirmerie', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est à l''infirmerie.', 5),(6, 'Sortie scolaire', 0, '1', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est en sortie scolaire.', 6)
,(7, 'Exclusion de l''établissement', 0, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est exclus de l''établissement.', 7),(8, 'Exclusion/inclusion', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est exclus mais présent au sein de l''établissement.', 8),(9, 'Exclusion de cours', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'DISCIPLINE', 'L''élève est exclus de cours.', 9),(10, 'Dispense (eleve présent)', 1, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est dispensé mais présent physiquement lors de la seance.', 10),(11, 'Dispense (élève non présent)', 1, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est dispensé et non présent physiquement lors de la seance.', 11),(12, 'Stage', 0, 'FAUX', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est en stage a l''extérieur de l''établissement.', 12),(13, 'Présent', 0, 'VRAI', 'FAUX', 'NON_PRECISE', 'NON_PRECISE', 'L''élève est présent.', 13);
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création des types par défaut pour la table 'a_types': ".$result_inter."<br />";
	}

	$sql="
INSERT INTO `a_types_statut` (`id`, `id_a_type`, `statut`) VALUES(1, 1, 'professeur'),(2, 1, 'cpe'),(3, 1, 'scolarite'),(4, 1, 'autre'),(5, 2, 'professeur'),(6, 2, 'cpe'),(7, 2, 'scolarite'),(8, 2, 'autre'),(9, 3, 'cpe'),(10, 3, 'scolarite'),(11, 3, 'autre'),(12, 4, 'professeur'),(13, 4, 'cpe'),(14, 4, 'scolarite'),(15, 4, 'autre'),(16, 5, 'professeur'),(17, 5, 'cpe'),(18, 5, 'scolarite'),(19, 5, 'autre'),(20, 6, 'professeur')
,(21, 6, 'cpe'),(22, 6, 'scolarite'),(23, 7, 'cpe'),(24, 7, 'scolarite'),(25, 8, 'cpe'),(26, 8, 'scolarite'),(27, 9, 'professeur'),(28, 9, 'cpe'),(29, 9, 'scolarite'),(30, 10, 'cpe'),(31, 10, 'scolarite'),(32, 11, 'cpe'),(33, 11, 'scolarite'),(34, 12, 'cpe'),(35, 12, 'scolarite'),(36, 13, 'professeur'),(37, 13, 'cpe'),(38, 13, 'scolarite'),(39, 13, 'autre');
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création des statuts autorisés pour les types de saisies par défaut (table 'a_types_statut'): ".$result_inter."<br />";
	}


}

#-----------------------------------------------------------------------------
#-- a_saisies
#-----------------------------------------------------------------------------
$test = sql_query1("SHOW TABLES LIKE 'a_saisies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_saisies'. ";
	$sql="
CREATE TABLE a_saisies
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(11) COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à null si aucun eleve n\'est saisi',
	commentaire TEXT COMMENT 'commentaire de l\'utilisateur',
	debut_abs DATETIME COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs DATETIME COMMENT 'Fin de l\'absence en timestamp UNIX',
	id_edt_creneau INTEGER(12) COMMENT 'identifiant du creneaux de l\'emploi du temps',
	id_edt_emplacement_cours INTEGER(12) COMMENT 'identifiant du cours de l\'emploi du temps',
	id_groupe INTEGER COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
	id_classe INTEGER COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
	id_aid INTEGER COMMENT 'identifiant de l\'aid pour lequel la saisie a ete effectuee',
	id_s_incidents INTEGER COMMENT 'identifiant de la saisie d\'incident discipline',
	modifie_par_utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a modifie en dernier le traitement',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_saisies_FI_1 (utilisateur_id),
	CONSTRAINT a_saisies_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login),
	INDEX a_saisies_FI_2 (eleve_id),
	CONSTRAINT a_saisies_FK_2
		FOREIGN KEY (eleve_id)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE,
	INDEX a_saisies_FI_3 (id_edt_creneau),
	CONSTRAINT a_saisies_FK_3
		FOREIGN KEY (id_edt_creneau)
		REFERENCES edt_creneaux (id_definie_periode)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_4 (id_edt_emplacement_cours),
	CONSTRAINT a_saisies_FK_4
		FOREIGN KEY (id_edt_emplacement_cours)
		REFERENCES edt_cours (id_cours)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_5 (id_groupe),
	CONSTRAINT a_saisies_FK_5
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_6 (id_classe),
	CONSTRAINT a_saisies_FK_6
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_7 (id_aid),
	CONSTRAINT a_saisies_FK_7
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE SET NULL
)ENGINE=MyISAM COMMENT='Chaque saisie d\'absence doit faire l\'objet d\'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l\'emploi du temps, le jours du cours etant precisé dans debut_abs.';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_saisies': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'a_traitements'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_traitements'. ";
	$sql="
CREATE TABLE a_traitements
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	a_type_id INTEGER(4) COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4) COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4) COMMENT 'cle etrangere de la justification de l\'absence',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	modifie_par_utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a modifie en dernier le traitement',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_traitements_FI_1 (utilisateur_id),
	CONSTRAINT a_traitements_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login),
	INDEX a_traitements_FI_2 (a_type_id),
	CONSTRAINT a_traitements_FK_2
		FOREIGN KEY (a_type_id)
		REFERENCES a_types (id)
		ON DELETE SET NULL,
	INDEX a_traitements_FI_3 (a_motif_id),
	CONSTRAINT a_traitements_FK_3
		FOREIGN KEY (a_motif_id)
		REFERENCES a_motifs (id)
		ON DELETE SET NULL,
	INDEX a_traitements_FI_4 (a_justification_id),
	CONSTRAINT a_traitements_FK_4
		FOREIGN KEY (a_justification_id)
		REFERENCES a_justifications (id)
		ON DELETE SET NULL
)ENGINE=MyISAM COMMENT='Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_traitements': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'j_traitements_saisies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_traitements_saisies'. ";
	$sql="
CREATE TABLE j_traitements_saisies
(
	a_saisie_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'absence saisie',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
	PRIMARY KEY (a_saisie_id,a_traitement_id),
	CONSTRAINT j_traitements_saisies_FK_1
		FOREIGN KEY (a_saisie_id)
		REFERENCES a_saisies (id)
		ON DELETE CASCADE,
	INDEX j_traitements_saisies_FI_2 (a_traitement_id),
	CONSTRAINT j_traitements_saisies_FK_2
		FOREIGN KEY (a_traitement_id)
		REFERENCES a_traitements (id)
		ON DELETE CASCADE
)ENGINE=MyISAM COMMENT='Table de jointure entre la saisie et le traitement des absences';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'j_traitements_saisies': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'a_notifications'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_notifications'. ";
	$sql="
CREATE TABLE a_notifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui envoi la notification',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement qu\'on notifie',
	type_notification INTEGER(5) COMMENT 'type de notification (0 : email, 1 : courrier, 2 : sms)',
	email VARCHAR(100) COMMENT 'email de destination (pour le type email)',
	telephone VARCHAR(100) COMMENT 'numero du telephone de destination (pour le type sms)',
	adr_id VARCHAR(10) COMMENT 'cle etrangere vers l\'adresse de destination (pour le type courrier)',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	statut_envoi INTEGER(5) default 0 COMMENT 'Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)',
	date_envoi DATETIME COMMENT 'Date envoi',
	erreur_message_envoi TEXT COMMENT 'Message d\'erreur retourné par le service d\'envoi',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_notifications_FI_1 (utilisateur_id),
	CONSTRAINT a_notifications_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX a_notifications_FI_2 (a_traitement_id),
	CONSTRAINT a_notifications_FK_2
		FOREIGN KEY (a_traitement_id)
		REFERENCES a_traitements (id)
		ON DELETE CASCADE,
	INDEX a_notifications_FI_3 (adr_id),
	CONSTRAINT a_notifications_FK_3
		FOREIGN KEY (adr_id)
		REFERENCES resp_adr (adr_id)
		ON DELETE SET NULL
)ENGINE=MyISAM COMMENT='Notification (a la famille) des absences';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_notifications': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'j_notifications_resp_pers'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_notifications_resp_pers'. ";
	$sql="
CREATE TABLE j_notifications_resp_pers
(
	a_notification_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de la notification',
	pers_id VARCHAR(10)  NOT NULL COMMENT 'cle etrangere des personnes',
	PRIMARY KEY (a_notification_id,pers_id),
	CONSTRAINT j_notifications_resp_pers_FK_1
		FOREIGN KEY (a_notification_id)
		REFERENCES a_notifications (id)
		ON DELETE CASCADE,
	INDEX j_notifications_resp_pers_FI_2 (pers_id),
	CONSTRAINT j_notifications_resp_pers_FK_2
		FOREIGN KEY (pers_id)
		REFERENCES resp_pers (pers_id)
		ON DELETE CASCADE
)ENGINE=MyISAM COMMENT='Table de jointure entre la notification et les personnes dont on va mettre le nom dans le message.';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_notifications': ".$result_inter."<br />";
	}
}


$test = sql_query1("SHOW TABLES LIKE 'matieres_app_delais'");
if ($test == -1) {
	$result .= "<br />Création de la table 'matieres_app_delais'. ";
	$sql="CREATE TABLE matieres_app_delais (periode int(11) NOT NULL default '0', id_groupe int(11) NOT NULL default '0', date_limite TIMESTAMP NOT NULL, PRIMARY KEY  (periode,id_groupe), INDEX id_groupe (id_groupe));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'matieres_app_delais': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'edt_semaines'");
if ($test == -1) {
	$result .= "<br />Création de la table 'edt_semaines'. ";
	$sql="CREATE TABLE edt_semaines (id_edt_semaine int(11) NOT NULL auto_increment,num_edt_semaine int(11) NOT NULL default '0',type_edt_semaine varchar(10) NOT NULL default '', num_semaines_etab int(11) NOT NULL default '0', PRIMARY KEY  (id_edt_semaine));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'edt_semaines': ".$result_inter."<br />";
	}
}

//==========================================================
// Modification Delineau
$result .= "<br /><br /><strong>Ajout d'une table pour les \"super-gestionnaires\" d'AID :</strong><br />";
$result .= "<br />&nbsp;->Tentative de création de la table j_aidcateg_super_gestionnaires.<br />";
$test = sql_query1("SHOW TABLES LIKE 'j_aidcateg_super_gestionnaires'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_aidcateg_super_gestionnaires (indice_aid INT NOT NULL ,id_utilisateur VARCHAR( 50 ) NOT NULL);");
	if ($result_inter == '')
	$result .= msj_ok("La table j_aidcateg_super_gestionnaires a été créée !");
	else
	$result .= $result_inter."<br />";
} else {
		$result .= msj_present("La table j_aidcateg_super_gestionnaires existe déjà.");
}


// Modification Eric
// ============= Insertion d'un champ pour le module discipline

$sql = "SELECT commentaire FROM s_incidents LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE `s_incidents` ADD `commentaire` TEXT NOT NULL ";
    $req_add_rank = mysql_query($sql_request);
    if ($req_add_rank) {
        $result .= "<p style=\"color:green;\">Ajout du champ commentaire dans la table <strong>s_incidents</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ commentaire à la table <strong>s_incidents</strong> : Erreur.</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ commentaire à la table <strong>s_incidents</strong> : déjà réalisé.</p>";
}

?>
