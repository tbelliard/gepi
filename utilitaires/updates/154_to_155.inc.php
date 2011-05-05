<?php
/*
 * $Id$
 *
 * Fichier de mise à jour de la version 1.5.4 à la version 1.5.5
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

$result .= "<br /><br /><b>Mise à jour vers la version 1.5.5" . $rc . $beta . " :</b><br />";

//===================================================
/*

// Exemples de sections:

// Ajout d'un champ dans setting

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

//===================================================

// Ajout d'une table

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

//===================================================

// Ajout d'un champ à une table

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

*/
//===================================================

$result .= "<br /><br /><b>Ajout d'une table 'j_groupes_visibilite' :</b><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_groupes_visibilite'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_groupes_visibilite (
			id INT(11) NOT NULL auto_increment,
			id_groupe INT(11) NOT NULL,
			domaine varchar(255) NOT NULL default '',
			visible varchar(255) NOT NULL default '',
			PRIMARY KEY (id),
			INDEX id_groupe_domaine (id_groupe, domaine)
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

//===================================================

$result .= "&nbsp;->Ajout d'un champ visible à la table 'ct_documents'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_documents LIKE 'visible';"));
if ($test_champ>0) {
	$result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
}
else {
	$query = mysql_query("ALTER TABLE ct_documents ADD visible CHAR(1) DEFAULT 'y' COMMENT 'Visibilité élève/parent du document joint' AFTER emplacement;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}

$result .= "&nbsp;->Ajout d'un champ visible à la table 'ct_devoirs_documents'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_devoirs_documents LIKE 'visible';"));
if ($test_champ>0) {
	$result .= "<font color=\"blue\">Le champ existe déjà.</font><br />";
}
else {
	$query = mysql_query("ALTER TABLE ct_devoirs_documents ADD visible CHAR(1) DEFAULT 'y' COMMENT 'Visibilité élève/parent du document joint' AFTER emplacement;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}

//===================================================

?>
