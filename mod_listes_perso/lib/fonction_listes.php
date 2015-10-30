<?php

/*
 *
 * Copyright 2015 Régis Bouguin
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

//=========================================================================================================
//Création provisoire des tables
//=========================================================================================================
function verifieTableCree() {
	global $mysqli;
	global $dbDb;
	
	// echo "création de mod_listes_perso_definition"."<br />" ;
	$sql_def = "CREATE TABLE IF NOT EXISTS `mod_listes_perso_definition` ("
	   . "`id` int(11) NOT NULL auto_increment, "
	   . "`nom` varchar(50) NOT NULL default '', "
	   . "`sexe` BOOLEAN default true, "
	   . "`classe` BOOLEAN default true, "
	   . "`photo` BOOLEAN default true, "
	   . "`propriétaire` VARCHAR( 50 ) NOT NULL COMMENT 'Nom du créateur de la liste', "
	   . "PRIMARY KEY  (`id`) "
	   . ") ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
	$query_def = mysqli_query($mysqli, $sql_def);
	if (!$query_def) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql_def."<br />" ;
	}
	
	// echo "création de mod_listes_perso_colonnes"."<br />" ;
	$sql_col = "CREATE TABLE IF NOT EXISTS `mod_listes_perso_colonnes` ("
	   . "`id` int(11) NOT NULL auto_increment, "
	   . "`id_def` int(11) NOT NULL, "
	   . "`titre` varchar(30) NOT NULL default '', "
	   . "`placement` int(11) NOT NULL, "
	   . "PRIMARY KEY  (`id`) "
	   . ") ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
	$query_col = mysqli_query($mysqli, $sql_col);
	if (!$query_col) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql_col."<br />" ;
	}
	// echo "création de mod_listes_perso_eleves"."<br />" ;
	$sql_elv = "CREATE TABLE IF NOT EXISTS `mod_listes_perso_eleves` ("
	   . "`id` int(11) NOT NULL auto_increment, "
	   . "`id_def` int(11) NOT NULL, "
	   . "`login` varchar(50) NOT NULL default '', "
	   . "PRIMARY KEY  (`id`), "
	   . "INDEX combinaison (`id_def`, `login`) "
	   . ") ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
	$query_elv = mysqli_query($mysqli, $sql_elv);
	if (!$query_elv) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql_elv."<br />" ;
	}

	// echo "création de mod_listes_perso_contenus"."<br />" ;	
	$sql_contenus = "CREATE TABLE IF NOT EXISTS `mod_listes_perso_contenus` ("
	   . "`id` int(11) NOT NULL auto_increment, "
	   . "`id_def` int(11) NOT NULL, "
	   . "`login` varchar(50) NOT NULL default '', "
	   . "`colonne` int(11) NOT NULL, "
	   . "`contenu` varchar(50) NOT NULL default '', "
	   . "PRIMARY KEY (`id`), "
	   . "INDEX contenu (`id_def`, `login`, `contenu`)"
	   . ") ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
	$query_contenus = mysqli_query($mysqli, $sql_contenus);
	if (!$query_contenus) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql_contenus."<br />" ;
	}
}


//=========================================================================================================
//                                     Générales
//=========================================================================================================
function EnregistreDroitListes($ouvre) {
	global $mysqli;
	$sql = "INSERT INTO `setting` (`NAME`, `VALUE`) VALUES ('GepiListePersonnelles', '".$ouvre."') "
	   . "ON DUPLICATE KEY UPDATE VALUE = '".$ouvre."' ";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function DroitSurListeOuvert() {
	global $mysqli;
	$retour = FALSE;
	$sql = "SELECT `VALUE` FROM `setting` WHERE `NAME`='GepiListePersonnelles' ";
	$query = mysqli_query($mysqli, $sql);
	$valeur = $query->fetch_object()->VALUE;
	if ($query->num_rows && $valeur === 'y') {
		$retour = TRUE;
	}
	return $retour;
}

function DonneeEnPostOuSession($enPost, $enSession, $defaut=NULL) {
	$retour = filter_input(INPUT_POST, $enPost) ? filter_input(INPUT_POST, $enPost) : (isset($_SESSION['liste_perso'][$enSession]) ? $_SESSION['liste_perso'][$enSession] : $defaut);
	return $retour;
}
//=========================================================================================================
//                                      Listes
//=========================================================================================================
function chargeListe($id) {
	if (!$id) {
		nouvelleListe($id);
	} else {
		litBase($id);
	}
}

function nouvelleListe($id) {
	$_SESSION['liste_perso']['id'] = $id;
	$_SESSION['liste_perso']['nom'] = '';
	$_SESSION['liste_perso']['sexe'] = FALSE;
	$_SESSION['liste_perso']['classe'] = FALSE;
	$_SESSION['liste_perso']['photo'] = FALSE;
	$_SESSION['liste_perso']['colonnes'] = array();
	$_SESSION['liste_perso']['nbColonne'] = 0 ;
}

function litBase($id) {
	$donnees = chargeTableau($id);
	if ($donnees) {
		$_SESSION['liste_perso']['colonnes'] = LitColonnes($id);
		$liste = $donnees->fetch_object();
		$_SESSION['liste_perso']['nom'] = $liste->nom;
		$_SESSION['liste_perso']['sexe'] = $liste->sexe;
		$_SESSION['liste_perso']['classe'] = $liste->classe;
		$_SESSION['liste_perso']['photo'] = $liste->photo;
		$_SESSION['liste_perso']['nbColonne'] = $_SESSION['liste_perso']['colonnes']->num_rows;
		$_SESSION['liste_perso']['id'] = $liste->id;
	}
}

function sauveDefListe($idListe,$nomListe, $sexeListe, $classeListe, $photoListe, $nbColonneListe) {
	global $mysqli;
	$sql = "INSERT INTO `mod_listes_perso_definition` "
	   . "SET "
	   . "`nom`= '$nomListe', "
	   . "`sexe`= '$sexeListe', "
	   . "`classe`= '$classeListe', "
	   . "`photo`= '$photoListe', "
	   . "`proprietaire`= '".$_SESSION['login']."' ";
	if (strlen((string)$idListe) !== 0) {$sql .= ", `id`=$idListe ";}
	$sql .= "ON DUPLICATE KEY UPDATE "
	   . "`nom`= '$nomListe', "
	   . "`sexe`= '$sexeListe', "
	   . "`classe`= '$classeListe', "
	   . "`photo`= '$photoListe' "
	   . ";";	
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	creeColonnes($idListe, $nbColonneListe);
	return TRUE;
}

function chargeTableau($idListe = NULL) {
	global $mysqli;
	$proprietaire = $_SESSION['login'];
	$sql = "SELECT * FROM `mod_listes_perso_definition` WHERE `proprietaire` = '$proprietaire' " ;
	if ($idListe !== NULL) {
		$sql .= "AND `id` LiKE '$idListe' ;" ;
	}
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de la lecture de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return $query;
}

function Dernier_id() {
	global $mysqli;
	$proprietaire = $_SESSION['login'];
	$sql = "SELECT MAX(id) as id FROM `mod_listes_perso_definition` WHERE `proprietaire` = '$proprietaire' " ;
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de la lecture de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	$last_id = $query->fetch_object()->id;
	return $last_id;
}
	


//=========================================================================================================
//                                      Colonnes
//=========================================================================================================
function CreeColonnes($idListe, $nouveauNombre) {
	global $mysqli;
	$colonnes = LitColonnes($idListe);
	$ancienNombre = $colonnes->num_rows;
	for($i=0;$i<($nouveauNombre-$ancienNombre);$i++) {
		$place = $ancienNombre + 1 + $i;
		$sql = "INSERT INTO `mod_listes_perso_colonnes` "
		   . "SET "
		   . "`id_def` = '$idListe', "
		   . "`titre` = '', "
		   . "`placement` = '$place' " ;
		//echo $sql."<br />" ;
		$query = mysqli_query($mysqli, $sql);
		if (!$query) {
			echo "Erreur lors de la lecture de la base ".mysqli_error($mysqli)."<br />" ;
			echo $sql."<br />" ;
			return FALSE;			
		}
	}
}

function LitColonnes($idListe) {
	global $mysqli;
	$sql = "SELECT * FROM `mod_listes_perso_colonnes` WHERE `id_def` = '$idListe' ORDER BY `placement` ASC " ;
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de la lecture de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return $query;
}

function SauveTitreColonne() {
	global $mysqli;
	$titre = filter_input(INPUT_POST, 'titre');
	$id = filter_input(INPUT_POST, 'id');
	//$id_def = filter_input(INPUT_POST, 'id_def');
	$sql = "UPDATE  `mod_listes_perso_colonnes` "
	   . "SET titre = '$titre' "
	   . "WHERE id = $id" ;
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;	
}

function DeplaceColonne($id, $newPlace, $idListe) {
	global $mysqli;
	// récupérer la place de la colonne
	$anciennePlace = PlaceColonne($id, $idListe);
	echo $anciennePlace.'<br />';
	
	
	
	return TRUE;
	$sql = "" ;
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;	
}

function PlaceColonne($id) {
	global $mysqli;
	$sql = "SELECT `placement` FROM `mod_listes_perso_colonnes` "
	   . "WHERE id = $id ";
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	$place = $query->fetch_object()->placement;
	return $place;
}

function SupprimeColonne($idListe, $colonne) {
	global $mysqli;
	// récupérer la place de la colonne
	//echo $idListe."→".$colonne;
	$anciennePlace = PlaceColonne($colonne);	
	//echo $idListe."→".$colonne;
	SupprimeColonnePourElv($idListe, $colonne);
	$sql = "DELETE FROM `mod_listes_perso_colonnes` WHERE `id` = '$colonne' ;" ;
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	// Mettre à jour les numéros de rang de colonnes
	crementePlace($idListe, $anciennePlace, -1);
	return TRUE;
}

function crementePlace($idListe, $debut, $deplacement) {
	global $mysqli;
	$sql = "UPDATE `mod_listes_perso_colonnes` SET placement = (placement + ($deplacement)) "
	   . "WHERE `id_def` = $idListe ";
	if($deplacement < 0) {
		$sql .= "AND `placement` > $debut " ;
	} else {
		$sql .= "AND `placement` < $debut " ;
	}
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;	
}

function SupprimeColonnePourElv($idListe, $colonne) {
	global $mysqli;
	$sql = "DELETE FROM `mod_listes_perso_contenus` WHERE `id_def` = '$idListe' AND `colonne` = '$colonne' ;" ;
	echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;
}



//=========================================================================================================
//                                      Élèves
//=========================================================================================================

function ChargeClasses() {
	global $utilisateur;
	$classe_col = $utilisateur->getClasses();
	var_dump($classe_col);
}

function ChargeEleves($idListe) {
	global $mysqli;
	$sql = "SELECT * FROM mod_listes_perso_eleves "
	   . "WHERE `id_def` = '$idListe' ";
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	if ($query->num_rows) {
		while ($id = $query->fetch_object()) {
			$listeId[] = $id->login;
		}
			$eleve_choisi_col = new PropelCollection();
			foreach ($listeId as $elv) {
				$query = EleveQuery::create();
				$query->findByLogin($elv);
				$eleve = $query->findOne();
				$eleve_choisi_col->add($eleve);
			}
		return $eleve_choisi_col;
	} else {
		return NULL;
	}	
}

function EnregistreElevesChoisis($idElevesChoisis, $idListe) {
	global $mysqli;
	foreach ($idElevesChoisis as $idEleve) {
		$sql = "INSERT INTO mod_listes_perso_eleves "
		   . "SET `id_def` = '$idListe', "
		   . "`login` = '$idEleve' "
		   . "ON DUPLICATE KEY UPDATE `login` = '$idEleve' ";
		//echo $sql."<br />" ;
		$query = mysqli_query($mysqli, $sql);
		if (!$query) {
			echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
			echo $sql."<br />" ;
			return FALSE;
		}
	}
	return TRUE;
}

function SupprimeEleve($login, $idListe) {
	global $mysqli;
	$sql = "DELETE FROM `mod_listes_perso_eleves` "
	   . "WHERE `login` = '$login' "
	   . "AND `id_def` = '$idListe'" ;	
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	SupprimeToutesColonnes($login, $idListe) ;
	return TRUE;
}

function ModifieCaseColonneEleve($login, $idListe,$idColonne ,$contenu, $id = NULL ) {
	global $mysqli;
	$sql = "INSERT INTO `mod_listes_perso_contenus` "
	   . "SET "
	   . "`contenu` = '$contenu', "
	   . "`colonne` = '$idColonne', "
	   . "`login` = '$login', "
	   . "`id_def` = '$idListe' " ;	
	if ($id !== NULL) {
		$sql .= ", `id` = '$id' " ;
	}
	$sql .= "ON DUPLICATE KEY UPDATE "
	   . "`contenu` = '$contenu'";
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;
}

function ChargeColonnesEleves($idListe, $eleve_choisi_col) {
	$tableauRetour = array();
	if (count($eleve_choisi_col)) {
		foreach ($eleve_choisi_col as $elv) {
			$tableauRetour[$elv->getLogin()] = ChargeCasesEleves($idListe, $elv);
		}
	}
	
	return $tableauRetour;
}

function ChargeCasesEleves($idListe, $elv) {
	global $mysqli;
	$tableauRetour = array();
	$sql = "SELECT * FROM `mod_listes_perso_contenus` "
	   . "WHERE `id_def` = '$idListe' "
	   . "AND `login` = '".$elv->getLogin()."' ";
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	if ($query->num_rows) {
		while ($case = $query->fetch_object()) {
			$tableauRetour[$case->colonne]['contenu'] = $case->contenu;
			$tableauRetour[$case->colonne]['id'] = $case->id;
		}

	}
	return $tableauRetour;
	
}

function SupprimeToutesColonnes($elv, $idListe) {
	global $mysqli;
	$sql = "DELETE FROM `mod_listes_perso_contenus` "
	   . "WHERE `id_def` = '$idListe' ";
	if ($elv !== NULL) {
		$sql .= "AND `login` = '$elv' ";
	}
	//echo $sql."<br />" ;
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de l'écriture dans la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;
}





