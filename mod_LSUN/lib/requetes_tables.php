<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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


// TODO : mettre ça ailleurs
$sql = "CREATE TABLE IF NOT EXISTS lsun_responsables ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique',"
	. "login varchar(50) NOT NULL,"
	. "PRIMARY KEY (id), UNIQUE KEY login (login) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_parcours_communs ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "periode int(11) NOT NULL COMMENT 'Periode de référence du parcours', "
	. "classe int(11) NOT NULL COMMENT 'id de la classe concernée', "
	. "codeParcours varchar(10) NOT NULL COMMENT 'Code officiel du parcours', "
	. "description text NOT NULL COMMENT 'Description du parcours', "
	. "PRIMARY KEY (id) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_epi_communs ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "periode int(11) NOT NULL COMMENT \"Periode de référence de l'epi\",  "
	. "codeEPI varchar(10) NOT NULL COMMENT \"Code officiel de l'epi\", "
	. "intituleEpi varchar(150) NOT NULL COMMENT \"Intitulé de l'epi\", "
	. "descriptionEpi text NOT NULL COMMENT \"Description de l'epi\", "
	. "PRIMARY KEY (id) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_j_epi_matieres ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "id_matiere int(11) NOT NULL COMMENT 'id de la matiere' , "
	. "modalite varchar(1) COMMENT \"modalite d'élection de la matiere\" , "
	. "id_epi int(11) NOT NULL COMMENT \"id de l'epi\", "
	. "PRIMARY KEY (id) , UNIQUE KEY couple (id_matiere , id_epi) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_j_epi_enseignements ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "id_epi int(11) NOT NULL COMMENT \"id de l'epi\", "
	. "id_enseignements int(11) NOT NULL COMMENT \"id de l'enseignement\", "
	. "aid int(11) NOT NULL COMMENT '0 si enseignement, 1 si AID', "
	. "PRIMARY KEY (id) , UNIQUE KEY couple (id_epi , id_enseignements , aid) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_j_epi_classes ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "id_epi int(11) NOT NULL COMMENT \"id de l'epi\", "
	. "id_classe int(11) NOT NULL COMMENT \"id de la classe\", "
	. "PRIMARY KEY (id) , UNIQUE KEY couple (id_epi , id_classe) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_j_epi_classe ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "id_epi int(11) NOT NULL COMMENT \"id de l'epi\", "
	. "id_classe int(11) NOT NULL COMMENT 'id de la classe concernée'"
	. "PRIMARY KEY (id) , UNIQUE KEY couple (id_epi , id_classe) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";

$sql = "ALTER TABLE `classes` ADD `mef_code` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `rn_abs_2`;";
//echo $sql;
$mysqli->query($sql);


$sql = "INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) "
	. "VALUES ('/mod_LSUN/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction du livret', '' ) "
	. "ON DUPLICATE KEY UPDATE administrateur = 'V'; ";
$mysqli->query($sql);
$sql = "INSERT INTO `droits` (`id`, `administrateur`, `professeur`, `cpe`, `scolarite`, `eleve`, `responsable`, `secours`, `autre`, `description`, `statut`) "
	. "VALUES ('/mod_LSUN/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'V', 'F', 'Extraction du livret', '' ) "
	. "ON DUPLICATE KEY UPDATE administrateur = 'V'; ";
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_ap_communs ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "intituleAP varchar(150) NOT NULL COMMENT \"Intitulé de l'AP\", "
	. "descriptionAP varchar(600) NOT NULL COMMENT \"Description de l'AP\", "
	. "PRIMARY KEY (id), UNIQUE KEY intituleAP (intituleAP) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_j_ap_aid ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "id_aid int(11) NOT NULL COMMENT \"id de l'aid\" , "
	. "id_ap int(11) NOT NULL COMMENT \"id de l'ap\", "
	. "PRIMARY KEY (id) , UNIQUE KEY ap (id_ap) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";

//echo $sql;
$mysqli->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS lsun_j_ap_matiere ("
	. "id int(11) NOT NULL auto_increment COMMENT 'identifiant unique', "
	. "id_enseignements varchar(10) NOT NULL COMMENT \"id de l'enseignement\", "
	. "modalite varchar(1)  COMMENT \"modalite d'élection de la matiere\" , "
	. "id_ap int(11) NOT NULL COMMENT \"id de l'ap\", "
	. "PRIMARY KEY (id) , UNIQUE KEY couple (id_enseignements , id_ap) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci; ";

// echo $sql;
$mysqli->query($sql);

