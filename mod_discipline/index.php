<?php

/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";
// Begin standart header
$niveau_arbo = 1;

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

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

include "../class_php/class_menu_general.php";

//**************** EN-TETE *****************
$titre_page = "Discipline : Index";
include_once("../lib/header_template.inc.php");
//**************** FIN EN-TETE *****************
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************

****************************************************************/

$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
if($mod_disc_terme_incident=="") {$mod_disc_terme_incident="incident";}
$mod_disc_terme_sanction=getSettingValue('mod_disc_terme_sanction');
if($mod_disc_terme_sanction=="") {$mod_disc_terme_sanction="sanction";}

$mod_disc_terme_avertissement_fin_periode=getSettingValue('mod_disc_terme_avertissement_fin_periode');
if($mod_disc_terme_avertissement_fin_periode=="") {$mod_disc_terme_avertissement_fin_periode="avertissement de fin de période";}

//debug_var();
/*
echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

echo "</p>\n";

echo "<p>Ce module est destiné à saisir et suivre les incidents et sanctions.</p>\n";

 */

//*********************************
// création des tables si besoin
//*********************************

$sql="CREATE TABLE IF NOT EXISTS s_incidents (
id_incident INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
declarant VARCHAR( 50 ) NOT NULL ,
date DATE NOT NULL ,
heure VARCHAR( 20 ) NOT NULL ,
id_lieu INT( 11 ) NOT NULL ,
nature VARCHAR( 255 ) NOT NULL ,
description TEXT NOT NULL,
etat VARCHAR( 20 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);
// Avec cette table on ne gère pas un historique des modifications de déclaration...

$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_incidents LIKE 'id_lieu'"));
if ($test1 == 0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_incidents ADD id_lieu INT( 11 ) NOT NULL AFTER heure;");
}

$sql="SHOW TABLES LIKE 's_qualites';";
$test_table=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_table)==0) {
	$sql="CREATE TABLE IF NOT EXISTS s_qualites (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	qualite VARCHAR( 50 ) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$creation=mysqli_query($GLOBALS["mysqli"], $sql);
	if($creation) {
		$tab_qualite=array("Responsable","Victime","Témoin","Autre");
		for($loop=0;$loop<count($tab_qualite);$loop++) {
			$sql="SELECT 1=1 FROM s_qualites WHERE qualite='".$tab_qualite[$loop]."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$sql="INSERT INTO s_qualites SET qualite='".$tab_qualite[$loop]."';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
	}
}

$sql="SHOW TABLES LIKE 's_types_sanctions2';";
$test_table=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_table)==0) {
	echo "<p style='color:red'>Une mise à jour de la base est requise !</p>\n";
	/*
	$sql="CREATE TABLE IF NOT EXISTS s_types_sanctions2 (
	id_nature INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	nature VARCHAR( 255 ) NOT NULL,
	type VARCHAR( 255 ) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$creation=mysql_query($sql);
	if($creation) {
		$tab_type=array("Exclusion", "Retenue", "Travail", "Avertissement travail","Avertissement comportement");
		for($loop=0;$loop<count($tab_type);$loop++) {
			$sql="SELECT 1=1 FROM s_types_sanctions2 WHERE nature='".$tab_type[$loop]."';";
			//echo "$sql<br />";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				if($loop<3) {$type=mb_strtolower($tab_type[$loop]);} else {$type="autre";}
				$sql="INSERT INTO s_types_sanctions2 SET nature='".$tab_type[$loop]."', type='".$type."';";
				$insert=mysql_query($sql);
			}
		}
	}
	*/
}

$sql="CREATE TABLE IF NOT EXISTS s_autres_sanctions (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_sanction INT( 11 ) NOT NULL ,
id_nature INT( 11 ) NOT NULL ,
description TEXT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="SHOW TABLES LIKE 's_mesures';";
$test_table=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_table)==0) {
	$sql="CREATE TABLE IF NOT EXISTS s_mesures (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	type ENUM('prise','demandee') ,
	mesure VARCHAR( 50 ) NOT NULL ,
	commentaire TEXT NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$creation=mysqli_query($GLOBALS["mysqli"], $sql);
	if($creation) {
		// Mesures prises
		$tab_mesure=array("Travail supplémentaire","Mot dans le carnet de liaison");
		for($loop=0;$loop<count($tab_mesure);$loop++) {
			$sql="SELECT 1=1 FROM s_mesures WHERE mesure='".$tab_mesure[$loop]."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$sql="INSERT INTO s_mesures SET mesure='".$tab_mesure[$loop]."', type='prise';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
	
		// Mesures demandées
		$tab_mesure=array("Retenue","Exclusion");
		for($loop=0;$loop<count($tab_mesure);$loop++) {
			$sql="SELECT 1=1 FROM s_mesures WHERE mesure='".$tab_mesure[$loop]."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$sql="INSERT INTO s_mesures SET mesure='".$tab_mesure[$loop]."', type='demandee';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
	}
}

$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_mesures LIKE 'commentaire'"));
if ($test1 == 0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_mesures ADD commentaire TEXT NOT NULL AFTER mesure;");
}

$sql="CREATE TABLE IF NOT EXISTS s_traitement_incident (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_incident INT( 11 ) NOT NULL ,
login_ele VARCHAR( 50 ) NOT NULL ,
login_u VARCHAR( 50 ) NOT NULL ,
id_mesure INT( 11 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="SHOW TABLES LIKE 's_lieux_incidents';";
$test_table=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test_table)==0) {
	$sql="CREATE TABLE IF NOT EXISTS s_lieux_incidents (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	lieu VARCHAR( 255 ) NOT NULL
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$creation=mysqli_query($GLOBALS["mysqli"], $sql);
	if($creation) {
		$tab_lieu=array("Classe","Couloir","Cour","Réfectoire","Autre");
		for($loop=0;$loop<count($tab_lieu);$loop++) {
			$sql="SELECT 1=1 FROM s_lieux_incidents WHERE lieu='".$tab_lieu[$loop]."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$sql="INSERT INTO s_lieux_incidents SET lieu='".$tab_lieu[$loop]."';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
	}
}

$sql="CREATE TABLE IF NOT EXISTS s_protagonistes (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_incident INT NOT NULL ,
login VARCHAR( 50 ) NOT NULL ,
statut VARCHAR( 50 ) NOT NULL ,
qualite VARCHAR( 50 ) NOT NULL,
avertie ENUM('N','O') NOT NULL DEFAULT 'N'
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_protagonistes LIKE 'avertie'"));
if ($test1 == 0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_protagonistes ADD avertie ENUM('N','O') NOT NULL DEFAULT 'N' AFTER qualite;");
}

$sql="CREATE TABLE IF NOT EXISTS s_sanctions (
id_sanction INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login VARCHAR( 50 ) NOT NULL ,
description TEXT NOT NULL ,
nature VARCHAR( 255 ) NOT NULL ,
id_nature_sanction INT(11),
effectuee ENUM( 'N', 'O' ) NOT NULL ,
id_incident INT( 11 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS s_communication (
id_communication INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_incident INT( 11 ) NOT NULL ,
login VARCHAR( 50 ) NOT NULL ,
nature VARCHAR( 255 ) NOT NULL ,
description TEXT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS s_travail (
id_travail INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_sanction INT( 11 ) NOT NULL ,
date_retour DATE NOT NULL ,
heure_retour VARCHAR( 20 ) NOT NULL ,
travail TEXT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS s_retenues (
id_retenue INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_sanction INT( 11 ) NOT NULL ,
date DATE NOT NULL ,
heure_debut VARCHAR( 20 ) NOT NULL ,
duree FLOAT NOT NULL ,
travail TEXT NOT NULL ,
lieu VARCHAR( 255 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS s_exclusions (
id_exclusion INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_sanction INT( 11 ) NOT NULL ,
date_debut DATE NOT NULL ,
heure_debut VARCHAR( 20 ) NOT NULL ,
date_fin DATE NOT NULL ,
heure_fin VARCHAR( 20 ) NOT NULL,
travail TEXT NOT NULL ,
lieu VARCHAR( 255 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS s_alerte_mail (id int(11) unsigned NOT NULL auto_increment, id_classe smallint(6) unsigned NOT NULL, destinataire varchar(50) NOT NULL default '', PRIMARY KEY (id), INDEX (id_classe,destinataire)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$creation=mysqli_query($GLOBALS["mysqli"], $sql);

$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM s_incidents LIKE 'message_id';"));
if ($test1 == 0) {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE s_incidents ADD message_id VARCHAR(50) NOT NULL;");
}

// L'état effectué ou non d'une sanction est dans la table s_sanctions plutôt s_retenues ou s_travail parce qu'il est plus simple de taper sur s_sanctions plutôt que sur les deux tables s_retenues ou s_travail

//***********************************
// Fin création des tables si besoin
//***********************************

$phrase_commentaire="";

$menuPage=array();
$menuTitre=array();

$nouveauItem = new itemGeneral();

//Début de la table configuration
//if($_SESSION['statut']=='administrateur') { 
if(($_SESSION['statut']=='administrateur')||
	(($_SESSION['statut']=='cpe')&&(
			(getSettingAOui('GepiDiscDefinirLieuxCpe'))||
			(getSettingAOui('GepiDiscDefinirRolesCpe'))||
			(getSettingAOui('GepiDiscDefinirMesuresCpe'))||
			(getSettingAOui('GepiDiscDefinirSanctionsCpe'))||
			(getSettingAOui('GepiDiscDefinirNaturesCpe'))||
			(getSettingAOui('GepiDiscDefinirCategoriesCpe'))||
			(getSettingAOui('GepiDiscDefinirDestAlertesCpe'))
		)
	)||
	(($_SESSION['statut']=='scolarite')&&(
			(getSettingAOui('GepiDiscDefinirLieuxScol'))||
			(getSettingAOui('GepiDiscDefinirRolesScol'))||
			(getSettingAOui('GepiDiscDefinirMesuresScol'))||
			(getSettingAOui('GepiDiscDefinirSanctionsScol'))||
			(getSettingAOui('GepiDiscDefinirNaturesScol'))||
			(getSettingAOui('GepiDiscDefinirCategoriesScol'))||
			(getSettingAOui('GepiDiscDefinirDestAlertesScol'))
		)
	)
) { 
/* ===== Titre du menu ===== */
	$menuTitre[]=new menuGeneral;
	end($menuTitre);
	$a = key($menuTitre);
	$menuTitre[$a]->classe='accueil';
	$menuTitre[$a]->icone['chemin']='../images/icons/control-center.png';
	$menuTitre[$a]->icone['titre']='';
	$menuTitre[$a]->icone['alt']="";
	$menuTitre[$a]->texte='Configuration du module';
	$menuTitre[$a]->indexMenu=$a;
	
/* ===== Item du menu ===== */
/*
	echo "<table class='menu' summary='Discipline'>\n";
	echo "<tr>\n";
	echo "<th colspan='2'><img src='../images/icons/control-center.png' alt='Configuration du module discipline' class='link'/> - Configuration du module</th>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/definir_lieux.php'>Définition des lieux</a>";
	echo "</td>\n";
	echo "<td>Définir la liste des lieux des incidents.</td>\n";
	echo "</tr>\n";
*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_lieux.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirLieuxCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirLieuxScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des lieux" ;
			$nouveauItem->expli="Définir la liste des lieux des ".$mod_disc_terme_incident."s." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);
/*
	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/definir_roles.php'>Définition des rôles</a>";
	echo "</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_roles.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirRolesCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirRolesScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des rôles" ;
			$nouveauItem->expli="Définir la liste des rôles des protagonistes." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);
/*
	
	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/definir_mesures.php'>Définition des mesures</a>";
	echo "</td>\n";
	echo "<td>Définir la liste des mesures prises comme suite à un incident.</td>\n";
	echo "</tr>\n";
*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_mesures.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirMesuresCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirMesuresScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des mesures" ;
			$nouveauItem->expli="Définir la liste des mesures prises comme suite à un ".$mod_disc_terme_incident."." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);
/*
	
	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/definir_autres_sanctions.php'>Définition des types de sanctions</a>";
	echo "</td>\n";
	echo "<td>Définir la liste des sanctions pouvant être prises comme suite à un incident.</td>\n";
	echo "</tr>\n";
*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_autres_sanctions.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirSanctionsCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirSanctionsScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des types de ".$mod_disc_terme_sanction."s" ;
			$nouveauItem->expli="Définir la liste des ".$mod_disc_terme_sanction."s pouvant être prises comme suite à un ".$mod_disc_terme_incident."." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);


	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_natures.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirNaturesCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirNaturesScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des natures d'".$mod_disc_terme_incident."s" ;
			$nouveauItem->expli="Définir les natures d'".$mod_disc_terme_incident."s (<em>liste indicative ou liste imposée</em>)." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);


	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_categories.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirCategoriesCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirCategoriesScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des catégories d'".$mod_disc_terme_incident."s" ;
			$nouveauItem->expli="Définir les catégories d'".$mod_disc_terme_incident."s (<em>à des fins de statistiques</em>)." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);


	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/definir_bilan_periode.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if($_SESSION['statut']=='administrateur') {
			$acces_ok="y";

			$nouveauItem->titre="Définition des types d'".$mod_disc_terme_avertissement_fin_periode."s" ;
			$nouveauItem->expli="Définir les types d'".$mod_disc_terme_avertissement_fin_periode."s." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);

/*
	

	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/destinataires_alertes.php'>Définition des destinataires d'alertes</a>";
	echo "</td>\n";
	echo "<td>Permet de définir la liste des utilisateurs recevant un mail lors de la saisie/modification d'un incident.</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/destinataires_alertes.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$acces_ok="n";
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiDiscDefinirDestAlertesCpe')))||
		(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiDiscDefinirDestAlertesScol')))) {
			$acces_ok="y";

			$nouveauItem->titre="Définition des destinataires d'alertes" ;
			$nouveauItem->expli="Permet de définir la liste des utilisateurs recevant un mail lors de la saisie/modification d'un ".$mod_disc_terme_incident."." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
	}
	unset($nouveauItem);
	
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/delegation.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->titre="Gestion des délégations d'exclusions temporaires" ;
		$nouveauItem->expli="Permet de gérer la liste des délégataires pour la génération des courriers d'exclusion temporaire" ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

	if(($_SESSION['statut']=='administrateur')||
	(($_SESSION['statut']=='cpe')&&(getSettingAOui('OOoUploadCpeDiscipline')))||
	(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OOoUploadScolDiscipline')))) {
		$nouveauItem = new itemGeneral();
		// L'ancre pose un pb de test de droit.
		$nouveauItem->chemin='/mod_ooo/gerer_modeles_ooo.php#MODULE_DISCIPLINE';
		//$nouveauItem->chemin='/mod_ooo/gerer_modeles_ooo.php';
		if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
		{
			$nouveauItem->titre="Gestion des modèles OOo" ;
			$nouveauItem->expli="Permet de gérer les modèles OOo associés au module Discipline." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
		unset($nouveauItem);
	}
}
//fin de la table configuration



//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";

/*
// Table Signaler / saisir un incident
echo "<table class='menu' summary='Discipline'>\n";
echo "<tr>\n";
echo "<th colspan='2'><img src='../images/icons/saisie.png' alt='Signaler ou saisir un incident' class='link'/> - Signaler ou saisir un incident</th>\n";
echo "</tr>\n";
 * /
 
/* ===== Titre du menu ===== */

  $menuTitre[]=new menuGeneral;
  end($menuTitre);
  $a = key($menuTitre);
  $menuTitre[$a]->classe='accueil';
  $menuTitre[$a]->icone['chemin']='../images/icons/saisie.png';
  $menuTitre[$a]->icone['titre']='';
  $menuTitre[$a]->icone['alt']="";
  $menuTitre[$a]->texte=' Signaler ou saisir un '.$mod_disc_terme_incident;
  $menuTitre[$a]->indexMenu=$a;
	
/* ===== Item du menu ===== */
  
/* 
echo "<tr>\n";
echo "<td width='30%'><a href='../mod_discipline/saisie_incident.php'>Signaler/saisir un incident</a>";
echo "</td>\n";
echo "<td>Signaler et décrire (nature, date, horaire, lieu, ...) un incident.<br />Indiquer les mesures prises ou demandées</td>\n";
echo "</tr>\n";
*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/saisie_incident.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->titre="Signaler/saisir un ".$mod_disc_terme_incident ;
		$nouveauItem->expli="Signaler et décrire (<em>nature, date, horaire, lieu,...</em>) un ".$mod_disc_terme_incident.".<br />Indiquer les mesures prises ou demandées" ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='scolarite') ||
($_SESSION['statut']=='professeur') ||
($_SESSION['statut']=='autre')) {

	$temoin = false;

	// 20130610: Vérifier cette requête... pb de longueur?

	$sql="SELECT 1=1 FROM s_incidents si
	LEFT JOIN s_protagonistes sp ON sp.id_incident=si.id_incident
	WHERE sp.id_incident IS NULL;";
	//echo "$sql<br />";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";
	if(mysqli_num_rows($test)>0) {
		$temoin = true;
		/*
		echo "<tr>\n";
		echo "<td width='30%'><a href='../mod_discipline/incidents_sans_protagonistes.php'>Incidents sans protagonistes</a>";
		echo "</td>\n";
		echo "<td>Liste des incidents signalés sans protagonistes.</td>\n";
		echo "</tr>\n";
		 */
	  $nouveauItem = new itemGeneral();
	  $nouveauItem->chemin='/mod_discipline/incidents_sans_protagonistes.php';
	  if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	  {
		  $nouveauItem->titre=ucfirst($mod_disc_terme_incident)."s sans protagonistes" ;
		  $nouveauItem->expli="Liste des ".$mod_disc_terme_incident."s signalés sans protagonistes." ;
		  $nouveauItem->indexMenu=$a;
		  $menuPage[]=$nouveauItem;
	  }
	  unset($nouveauItem);
	}
}

// 20130610: Vérifier cette requête: Mettre LIMIT 1
$sql="SELECT 1=1 FROM s_incidents si WHERE si.declarant='".$_SESSION['login']."' AND si.nature='' AND etat!='clos';";
//echo "$sql<br />";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";
$nb_incidents_incomplets=mysqli_num_rows($test);
if($nb_incidents_incomplets>0) {

  $nouveauItem = new itemGeneral();
  $nouveauItem->chemin="/mod_discipline/traiter_incident.php";
	  if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	  {
		$nouveauItem->chemin="/mod_discipline/traiter_incident.php?declarant_incident=".$_SESSION['login']."&amp;nature_incident=" ;
		$nouveauItem->titre="Incidents incomplets" ;
		if($nb_incidents_incomplets==1) {
			$aff_incident = "un ".$mod_disc_terme_incident;
		}
		else {
			$aff_incident = $nb_incidents_incomplets." ".$mod_disc_terme_incident."s";
		}
		$nouveauItem->expli="Vous avez signalé ".$aff_incident." sans préciser la nature de l'".$mod_disc_terme_incident.".<br />Pour faciliter la gestion des ".$mod_disc_terme_incident."s, il faudrait compléter." ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	  }
	  unset($nouveauItem);
/*
	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/traiter_incident.php?declarant_incident=".$_SESSION['login']."&amp;nature_incident='>Incidents incomplets</a>";
	echo "</td>\n";
	echo "<td style='color:red;'>Vous avez signalé ";
	if($nb_incidents_incomplets==1) {
			echo "un incident";
		}
		else {
			echo "$nb_incidents_incomplets incidents";
		}
	echo " sans préciser la nature de l'incident.<br />Pour faciliter la gestion des incidents, il faudrait compléter.\n";
	echo "</td>\n";
	echo "</tr>\n";
 */
}

/*

echo "</table>\n";
// fin de table saisir

	echo "</tr>\n";
 */
//Table Traiter
if(($_SESSION['statut']=='administrateur') || ($_SESSION['statut']=='cpe') || ($_SESSION['statut']=='scolarite')) {
/*
	echo "<table class='menu' summary='Discipline'>\n";
	echo "<tr>\n";
	echo "<th colspan='2'><img src='../images/icons/saisie.png' alt='Traiter un incident' class='link'/> - Traiter un incident</th>\n";
*/

/* ===== Titre du menu ===== */

  $menuTitre[]=new menuGeneral;
  end($menuTitre);
  $a = key($menuTitre);
  $menuTitre[$a]->classe='accueil';
  $menuTitre[$a]->icone['chemin']='../images/icons/saisie.png';
  $menuTitre[$a]->icone['titre']='';
  $menuTitre[$a]->icone['alt']="";
  $menuTitre[$a]->texte='Traiter un '.$mod_disc_terme_incident;
  $menuTitre[$a]->indexMenu=$a;
	
/* ===== Item du menu ===== */
  /*
	echo "<tr>\n";
  */
  
    $nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/traiter_incident.php';
	  if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	  {
  /*
	if ($temoin) {
		echo "<td width='30%'><a href='../mod_discipline/traiter_incident.php'></a> (<em>avec protagonistes</em>)";
	} else {
		echo "<td width='30%'><a href='../mod_discipline/traiter_incident.php'>Traiter les suites d'un incident</a>";
	}
	echo "</td>\n";
	echo "<td>Traiter les suites d'un incident : définir une punition ou une sanction</td>\n";
	echo "</tr>\n";
	
	echo "</table>\n";
   */
		
	$ajout_titre= "";
	if ($temoin) $ajout_titre= "(<em>avec protagonistes</em>)";
		  $nouveauItem->titre="Traiter les suites d'un ".$mod_disc_terme_incident." ".$ajout_titre;
		  $nouveauItem->expli="Traiter les suites d'un ".$mod_disc_terme_incident." : définir une punition ou une ".$mod_disc_terme_sanction ;
		  $nouveauItem->indexMenu=$a;
		  $menuPage[]=$nouveauItem;
	  }
	  unset($nouveauItem);
}
//Fin table traiter

/*
//Table afficher
echo "<table class='menu' summary='Discipline'>\n";
echo "<tr>\n";
echo "<th colspan='2'><img src='../images/icons/chercher.png' alt='consulter les incidents' class='link'/> - Consulter les incidents</th>\n";
echo "</tr>\n";

/* ===== Titre du menu ===== */

  $menuTitre[]=new menuGeneral;
  end($menuTitre);
  $a = key($menuTitre);
  $menuTitre[$a]->classe='accueil';
  $menuTitre[$a]->icone['chemin']='../images/icons/chercher.png';
  $menuTitre[$a]->icone['titre']='';
  $menuTitre[$a]->icone['alt']="";
  $menuTitre[$a]->texte='Consulter les '.$mod_disc_terme_incident.'s';
  $menuTitre[$a]->indexMenu=$a;

/* ===== Item du menu ===== */

if(($_SESSION['statut']=='administrateur') || ($_SESSION['statut']=='cpe') || ($_SESSION['statut']=='scolarite')) {

/*	echo "<tr>\n";
	echo "<td width='30%'><a href='../mod_discipline/liste_sanctions_jour.php'>Liste des sanctions du jour</a>";
	echo "</td>\n";
	echo "<td>Visualiser la liste des sanctions du jour (Exclusion, retenue, ...)</td>\n";
	echo "</tr>\n";
	*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/liste_sanctions_jour.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->titre="Liste des ".$mod_disc_terme_sanction."s du jour" ;
		$nouveauItem->expli="Visualiser la liste des ".$mod_disc_terme_sanction."s du jour (<em>Exclusion, retenue,...</em>)" ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);



	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/mod_discipline_extraction_ooo.php?mode=choix';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->titre="Extraction ODS" ;
		$nouveauItem->expli="Extraction ODS des ".$mod_disc_terme_incident."s et de leurs suites." ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);



	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/stats2/index.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->titre="Accèder aux statistiques" ;
		$nouveauItem->expli="Sélectionner la période de traitement, les données à traiter (<em>établissement, classes, elèves,...</em>) en appliquant (<em>ou non</em>) des filtres afin d'obtenir des bilans plus ou moins détaillés. <br />Visualiser les évolutions sous la forme de graphiques. Editer le Top 10, ..." ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
/*
	
	echo "<tr>\n";
	//echo "<td width='30%'>Effectuer des recherches/statistiques diverses<br /><span style='color: red;'>A FAIRE</span>";
		echo "<td width='30%'><a href='disc_stat.php'>Effectuer des recherches/statistiques diverses</a><br /><span style='color: red;'>A FAIRE</span>";
	echo "</td>\n";
	echo "<td>Pouvoir lister les incidents ayant eu tel élève pour protagoniste (<em>en précisant ou non le rôle dans l'incident</em>), le nombre de travaux, de retenues, d'exclusions,... entre telle et telle date,...</td>\n";
	echo "</tr>\n";
	*/
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/disc_stat.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->titre="Effectuer des recherches/statistiques diverses" ;
		$nouveauItem->expli="Pouvoir lister les ".$mod_disc_terme_incident."s ayant eu tel élève pour protagoniste (<em>en précisant ou non le rôle dans l'".$mod_disc_terme_incident."</em>), le nombre de travaux, de retenues, d'exclusions,... entre telle et telle date,..." ;
		$nouveauItem->indexMenu=$a;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

}
elseif (($_SESSION['statut']=='professeur') || ($_SESSION['statut']=='autre')) {
	//$sql="SELECT 1=1 FROM s_protagonistes WHERE login='".$_SESSION['login']."';";
	// declarant ou protagoniste
  

	// 20130610: Vérifier cette requête
	//$sql="SELECT 1=1 FROM s_protagonistes, s_incidents WHERE ((login='".$_SESSION['login']."')||(declarant='".$_SESSION['login']."'));";
	$sql="(SELECT 1=1 FROM s_protagonistes WHERE login='".$_SESSION['login']."' LIMIT 1) UNION (SELECT 1=1 FROM s_incidents WHERE declarant='".$_SESSION['login']."' LIMIT 1);";
	//echo "$sql<br />";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";
	if((mysqli_num_rows($test)>0)) { //on a bien un prof ou statut autre comme déclarant ou un protagoniste
	  /*
		echo "<tr>\n";
		echo "<td width='30%'><a href='../mod_discipline/traiter_incident.php'>Consulter les suites des incidents</a>";
		echo "</td>\n";
		echo "<td>Visualiser la liste des incidents déclarés et leurs traitements.</td>\n";
		echo "</tr>\n";
		*/
	  $nouveauItem = new itemGeneral();
	  $nouveauItem->chemin='/mod_discipline/traiter_incident.php';
	  if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	  {
		  $nouveauItem->titre="Consulter les suites des ".$mod_disc_terme_incident."s" ;
		  $nouveauItem->expli="Visualiser la liste des ".$mod_disc_terme_incident."s déclarés et leurs traitements." ;
		  $nouveauItem->indexMenu=$a;
		  $menuPage[]=$nouveauItem;
	  }
	unset($nouveauItem);
	} else { //le prof n'est ni déclarant ni protagoniste. Pour un elv dont il est PP y a t--il des incidents de déclaré ?

		// 20130610: Vérifier ces requêtes: Peut-être ajouter des LIMIT 1

		$sql="SELECT 1=1 FROM j_eleves_professeurs jep, s_protagonistes sp 
						  WHERE sp.login=jep.login
						  AND jep.professeur='".$_SESSION['login']."'
						  LIMIT 1;";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql); // prof principal
		//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";
		if (getSettingValue("visuDiscProfClasses")=='yes') {
		  $sql1="SELECT 1=1	FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_eleves_classes jec, s_protagonistes sp
	WHERE sp.login=jec.login
		AND jec.id_classe=jgc.id_classe
		AND jgp.id_groupe =  jgc.id_groupe
		AND jgp.login = '".$_SESSION['login']."'
		LIMIT 1;";
		//echo "$sql<br />";
		  $test1=mysqli_query($GLOBALS["mysqli"], $sql1); // prof de la classe autorisé à voir
		//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";
		}
		if (getSettingValue("visuDiscProfGroupes")=='yes') {
		  $sql2="SELECT 1=1 FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, s_protagonistes sp
							WHERE jeg.id_groupe=jgp.id_groupe
							AND sp.login=jeg.login
							AND jgp.login='".$_SESSION['login']."'
							LIMIT 1;";
			//echo "$sql<br />";
		  $test2=mysqli_query($GLOBALS["mysqli"], $sql2); // prof du groupe autorisé à voir
		//echo strftime("%Y-%m-%d %H:%M:%S")."<br />\n";
		}
		  $nouveauItem = new itemGeneral();
		if ((mysqli_num_rows($test)>0) ||
				(getSettingValue("visuDiscProfClasses")=='yes' && mysqli_num_rows($test1)>0) ||
				(getSettingValue("visuDiscProfGroupes")=='yes' && mysqli_num_rows($test2)>0)) { //Oui
		  /*
			echo "<tr>\n";
			echo "<td width='30%'><a href='../mod_discipline/traiter_incident.php'>Consulter les suites des incidents</a>";
			echo "</td>\n";
			echo "<td>Visualiser la liste des incidents déclarés et leurs traitements.</td>\n";
			echo "</tr>\n";
		*/
		  //$nouveauItem = new itemGeneral();
		  $nouveauItem->chemin='/mod_discipline/traiter_incident.php';
		  if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
		  {
			  $nouveauItem->titre="Consulter les suites des ".$mod_disc_terme_incident."s" ;
			  $nouveauItem->expli="Visualiser la liste des ".$mod_disc_terme_incident."s déclarés et leurs traitements." ;
			  $nouveauItem->indexMenu=$a;
			  $menuPage[]=$nouveauItem;
		  }
		//unset($nouveauItem);
		} else { //non
		  /*
			echo "<tr>\n";
			echo "<td width='30%'>Consulter les suites des incidents</a>";
			echo "</td>\n";
			echo "<td><p>Aucun incident (<em>avec protagoniste</em>) vous concernant n'est encore déclaré.</td>\n";
			echo "</tr>\n";
		   */
			$nouveauItem->chemin='/mod_discipline/index.php';
			$nouveauItem->titre="Consulter les suites des ".$mod_disc_terme_incident."s" ;
			$nouveauItem->expli="Aucun ".$mod_disc_terme_incident." (<em>avec protagoniste</em>) vous concernant n'est encore déclaré." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;

		}
		unset($nouveauItem);
	}
}


/* ===== Titre du menu ===== */

$acces_saisie_avertissement_fin_periode=acces_saisie_avertissement_fin_periode("");
if($_SESSION['statut']=='professeur') {
	$is_pp=is_pp($_SESSION['login']);
}

if(($acces_saisie_avertissement_fin_periode)||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('imprDiscProfAvtOOo')))||
(($_SESSION['statut']=='professeur')&&($is_pp))&&(getSettingAOui('imprDiscProfPAvtOOo'))) {
	$menuTitre[]=new menuGeneral;
	end($menuTitre);
	$a = key($menuTitre);
	$menuTitre[$a]->classe='accueil';
	$menuTitre[$a]->icone['chemin']='../images/icons/chercher.png';
	$menuTitre[$a]->icone['titre']='';
	$menuTitre[$a]->icone['alt']="";
	$menuTitre[$a]->texte=$mod_disc_terme_avertissement_fin_periode;
	$menuTitre[$a]->indexMenu=$a;

	/* ===== Item du menu ===== */
	if($acces_saisie_avertissement_fin_periode) {
		$nouveauItem = new itemGeneral();
		$nouveauItem->chemin='/mod_discipline/saisie_avertissement_fin_periode.php';
		if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
		{
			$nouveauItem->titre="Saisie ".$mod_disc_terme_avertissement_fin_periode ;
			$nouveauItem->expli="Saisir un ou des ".$mod_disc_terme_avertissement_fin_periode." saisis." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
		unset($nouveauItem);
	}

	/* ===== Item du menu ===== */
	if(($acces_saisie_avertissement_fin_periode)||
	(($_SESSION['statut']=='professeur')&&(getSettingAOui('imprDiscProfAvtOOo')))||
	(($_SESSION['statut']=='professeur')&&($is_pp))&&(getSettingAOui('imprDiscProfPAvtOOo'))) {
		$nouveauItem = new itemGeneral();
		$nouveauItem->chemin='/mod_discipline/imprimer_bilan_periode.php';
		if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
		{
			$nouveauItem->titre="Imprimer les ".$mod_disc_terme_avertissement_fin_periode ;
			$nouveauItem->expli="Imprimer les ".$mod_disc_terme_avertissement_fin_periode." saisis." ;
			$nouveauItem->indexMenu=$a;
			$menuPage[]=$nouveauItem;
		}
		unset($nouveauItem);
	}
}


/*
echo "</table>\n";
//Fin table afficher


echo $phrase_commentaire;

echo "<p><br /></p>\n";

echo "<p><em>NOTES&nbsp;</em></p>\n";
echo "<ul>\n";
echo "<li><p>Une fois un incident clos, il ne peut plus être modifié et aucune sanction liée ne peut être ajoutée/modifiée/supprimée.</p></li>\n";
echo "<li><p>Le module ne conserve pas un historique des modifications d'un incident.<br />Si plusieurs personnes modifient un incident, elles doivent le faire en bonne intelligence.</p></li>\n";
echo "<li><p>Un professeur peut saisir un incident, mais ne peut pas saisir les sanctions.<br />
Un professeur ne peut modifier que les incidents (<em>non clos</em>) qu'il a lui-même déclaré.<br />Il ne peut consulter que les incidents (<em>et leurs suites</em>) qu'il a déclarés, ou dont il est protagoniste, ou encore dont un des élèves, dont il est professeur principal, est protagoniste.</p></li>\n";
//echo "<li><p><em>A FAIRE:</em> Ajouter des tests 'changement()' dans les pages de saisie pour ne pas quitter une étape sans enregistrer.</p></li>\n";
echo "<li><p><em>A FAIRE:</em> Permettre de consulter d'autres incidents que les siens propres.<br />Eventuellement avec limitation aux élèves de ses classes.</p></li>\n";
echo "<li><p><em>A FAIRE ENCORE:</em> Permettre d'archiver les incidents/sanctions d'une année et vider les tables incidents/sanctions lors de l'initialisation pour éviter des blagues avec les login élèves réattribués à de nouveaux élèves (<em>homonymie,...</em>)</p></li>\n";
//echo "<li><p></p></li>\n";
echo "</ul>\n";

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
*/


$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var2();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_discipline/mod_discipline_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuPage);
unset($menuTitre);

?>
