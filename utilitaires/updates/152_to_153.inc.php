<?php
/* 
 * $Id$
 *
 * Fichier de mise à jour de la version 1.5.2 à la version 1.5.3
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= "<font color='gree'>Champ XXX ajouté avec succès</font>";
 */

$result .= "<br /><br /><b>Mise à jour vers la version 1.5.3" . $rc . $beta . " :</b><br />";


$result .= "&nbsp;->Extension à 255 caractères du champ 'SESSION_ID' de la table 'log'<br />";
$query = mysql_query("ALTER TABLE `log` CHANGE `SESSION_ID` `SESSION_ID` VARCHAR( 255 ) NOT NULL;");
if ($query) {
        $result .= "<font color=\"green\">Ok !</font><br />";
} else {
        $result .= "<font color=\"red\">Erreur</font><br />";
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
		$result.="<font color=\"green\">Ok !</font><br />";

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
				$result.="<font color=\"green\">Ok !</font><br />";
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
				$result.="<font color=\"green\">Ok !</font><br />";
			}
		}
	}
	$result.="<br />";
}

$result .= "&nbsp;->Ajout d'un champ date_decompte à la table 'messages'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM messages LIKE 'date_decompte';"));
if ($test_date_decompte>0) {
	$result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
}
else {
	$query = mysql_query("ALTER TABLE messages ADD date_decompte INT NOT NULL DEFAULT '0';");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}


// Ajout d'une table pays
$test = sql_query1("SHOW TABLES LIKE 'pays'");
if ($test == -1) {
	$result .= "<br />Création de la table 'pays'. ";
	$sql="CREATE TABLE IF NOT EXISTS pays (code_pays VARCHAR( 50 ) NOT NULL, nom_court VARCHAR( 50 ) NOT NULL ,nom_long VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( code_pays ));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'pays': ".$result_inter."<br />";
	}
}

//---------------
// Ajouts d'index

$result .= add_index('absences_creneaux','heures_debut_fin','heuredebut_definie_periode, heurefin_definie_periode');
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





?>
