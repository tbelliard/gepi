<?php
/*
 * $Id$
 *
 * Fichier de mise à jour de la version 1.5.3 à la version 1.5.4
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

$result .= "<br /><br /><b>Mise à jour vers la version 1.5.4" . $rc . $beta . " :</b><br />";

//===================================================
//
//deja mis dans 153_to_1531
//
//$champ_courant=array('nom1', 'prenom1', 'nom2', 'prenom2');
//for($loop=0;$loop<count($champ_courant);$loop++) {
//	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'responsables'<br />";
//	$query = mysql_query("ALTER TABLE responsables CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
//	if ($query) {
//			$result .= "<font color=\"green\">Ok !</font><br />";
//	} else {
//			$result .= "<font color=\"red\">Erreur</font><br />";
//	}
//}
//
//$champ_courant=array('nom', 'prenom');
//for($loop=0;$loop<count($champ_courant);$loop++) {
//	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'resp_pers'<br />";
//	$query = mysql_query("ALTER TABLE resp_pers CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
//	if ($query) {
//			$result .= "<font color=\"green\">Ok !</font><br />";
//	} else {
//			$result .= "<font color=\"red\">Erreur</font><br />";
//	}
//}
//===================================================


// Ajout de paramètres pour l'import d'attributs depuis CAS
// Paramètre d'activation de la synchro à la volée Scribe NG

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_prenom'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_prenom', '');");
  if ($result_inter == '') {
    $result.="<font color=\"green\">Définition du paramètre cas_attribut_prenom : Ok !</font><br />";
  } else {
    $result.="<font color=\"red\">Définition du paramètre cas_attribut_prenom : Erreur !</font><br />";
  }
} else {
  $result .= "<font color=\"blue\">Le paramètre cas_attribut_prenom existe déjà dans la table setting.</font><br />";
}

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_nom'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_nom', '');");
  if ($result_inter == '') {
    $result.="<font color=\"green\">Définition du paramètre cas_attribut_nom : Ok !</font><br />";
  } else {
    $result.="<font color=\"red\">Définition du paramètre cas_attribut_nom : Erreur !</font><br />";
  }
} else {
  $result .= "<font color=\"blue\">Le paramètre cas_attribut_nom existe déjà dans la table setting.</font><br />";
}

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_email'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_email', '');");
  if ($result_inter == '') {
    $result.="<font color=\"green\">Définition du paramètre cas_attribut_email : Ok !</font><br />";
  } else {
    $result.="<font color=\"red\">Définition du paramètre cas_attribut_email : Erreur !</font><br />";
  }
} else {
  $result .= "<font color=\"blue\">Le paramètre cas_attribut_email existe déjà dans la table setting.</font><br />";
}


//===================================================
$result .= "<br /><br /><b>Ajout d'une table modeles_grilles_pdf :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'modeles_grilles_pdf'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modeles_grilles_pdf (
		id_modele INT(11) NOT NULL auto_increment,
		login varchar(50) NOT NULL default '',
		nom_modele varchar(255) NOT NULL,
		par_defaut ENUM('y','n') DEFAULT 'n',
		PRIMARY KEY (id_modele)
		);");
	if ($result_inter == '') {
		$result .= "<font color=\"green\">SUCCES !</font><br />";
	}
	else {
		$result .= "<font color=\"red\">ECHEC !</font><br />";
	}
} else {
		$result .= "<font color=\"blue\">La table existe déjà</font><br />";
}

$result .= "<br /><br /><b>Ajout d'une table modeles_grilles_pdf_valeurs :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'modeles_grilles_pdf_valeurs'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modeles_grilles_pdf_valeurs (
		id_modele INT(11) NOT NULL,
		nom varchar(255) NOT NULL default '',
		valeur varchar(255) NOT NULL,
		INDEX id_modele_champ (id_modele, nom)
		);");
	if ($result_inter == '') {
		$result .= "<font color=\"green\">SUCCES !</font><br />";
	}
	else {
		$result .= "<font color=\"red\">ECHEC !</font><br />";
	}
} else {
		$result .= "<font color=\"blue\">La table existe déjà</font><br />";
}

$result .= "<br /><br /><b>Ajout d'une table pour les lieux des absences :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'a_lieux'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS a_lieux (
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du lieu',
	commentaire TEXT   COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Lieu pour les types d\'absence ou les saisies';");
	if ($result_inter == '') {
		$result .= "<font color=\"green\">SUCCES !</font><br />";
	}
	else {
		$result .= "<font color=\"red\">ECHEC !</font><br />";
	}
} else {
		$result .= "<font color=\"blue\">La table existe déjà</font><br />";
}

$result .= "&nbsp;->Ajout d'un champ id_lieu à la table 'a_types'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_types LIKE 'id_lieu';"));
if ($test_date_decompte>0) {
	$result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
}
else {
	$query = mysql_query("ALTER TABLE a_types ADD id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'eleve' AFTER commentaire,
       ADD INDEX a_types_FI_1 (id_lieu),
       ADD CONSTRAINT a_types_FK_1
		FOREIGN KEY (id_lieu)
		REFERENCES a_lieux (id)
		ON DELETE SET NULL ;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}

$result .= "&nbsp;->Ajout d'un champ id_lieu à la table 'a_saisies'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies LIKE 'id_lieu';"));
if ($test_date_decompte>0) {
	$result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
}
else {
	$query = mysql_query("ALTER TABLE a_saisies ADD id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'eleve' AFTER modifie_par_utilisateur_id,
       ADD INDEX a_saisies_FI_9 (id_lieu),
        ADD CONSTRAINT a_saisies_FK_9
		FOREIGN KEY (id_lieu)
		REFERENCES a_lieux (id)
		ON DELETE SET NULL;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}

//===================================
$result .= "<br /><br /><b>Ajout d'une table pour les contrôles de cours :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'cc_dev'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cc_dev (id int(11) NOT NULL auto_increment, 
id_cn_dev int(11) NOT NULL default '0',
id_groupe int(11) NOT NULL default '0',
nom_court varchar(32) NOT NULL default '',
nom_complet varchar(64) NOT NULL default '',
description varchar(128) NOT NULL default '',
arrondir char(2) NOT NULL default 's1',
PRIMARY KEY  (id));");
	if ($result_inter == '') {
		$result .= "<font color=\"green\">SUCCES !</font><br />";
	}
	else {
		$result .= "<font color=\"red\">ECHEC !</font><br />";
	}
} else {
		$result .= "<font color=\"blue\">La table existe déjà</font><br />";
}


$result .= "<br /><b>Ajout d'une table pour les évaluations des contrôles de cours :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'cc_eval'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cc_eval (id int(11) NOT NULL auto_increment,
id_dev int(11) NOT NULL default '0',
nom_court varchar(32) NOT NULL default '',
nom_complet varchar(64) NOT NULL default '',
description varchar(128) NOT NULL default '',
date datetime NOT NULL default '0000-00-00 00:00:00',
note_sur int(11) default '5',
PRIMARY KEY  (id),
INDEX dev_date (id_dev, date));");
	if ($result_inter == '') {
		$result .= "<font color=\"green\">SUCCES !</font><br />";
	}
	else {
		$result .= "<font color=\"red\">ECHEC !</font><br />";
	}
} else {
		$result .= "<font color=\"blue\">La table existe déjà</font><br />";
}


$result .= "<br /><b>Ajout d'une table pour les notes des évaluations des contrôles de cours :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'cc_notes_eval'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cc_notes_eval ( login varchar(50) NOT NULL default '',
id_eval int(11) NOT NULL default '0',
note float(10,1) NOT NULL default '0.0',
statut char(1) NOT NULL default '',
comment text NOT NULL,
PRIMARY KEY  (login,id_eval));");
	if ($result_inter == '') {
		$result .= "<font color=\"green\">SUCCES !</font><br />";
	}
	else {
		$result .= "<font color=\"red\">ECHEC !</font><br />";
	}
} else {
		$result .= "<font color=\"blue\">La table existe déjà</font><br />";
}
//===================================

?>
