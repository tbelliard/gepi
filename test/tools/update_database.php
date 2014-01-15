#!/usr/bin/php 
<?php
/**
 * Exécute une requête et renvoie une erreur au besoin
 * @global string
 * @param string $requete La requête à traité
 * @return string l'erreur ou vide
 */
function traite_requete($requete = "") {
	global $pb_maj;
	$retour = "";
	$res = mysqli_query($GLOBALS["mysqli"], $requete);
	$erreur_no = ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false));
	if (!$erreur_no) {
		$retour = "";
	} else {
		switch ($erreur_no) {
			case "1060" :
				// le champ existe déjà : pas de problème
				$retour = "";
				break;
			case "1061" :
				// La clé existe déjà : pas de problème
				$retour = "";
				break;
			case "1062" :
				// Présence d'un doublon : création de la cléf impossible
				$retour = msj_erreur("Erreur (<strong>non critique</strong>) sur la requête : <i>" . $requete . "</i> (" . ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . " : " . ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . ")");
				$pb_maj = 'yes';
				break;
			case "1068" :
				// Des clés existent déjà : pas de problème
				$retour = "";
				break;
			case "1069" :
				// trop d'index existent déjà pour cette table
				$retour = msj_erreur("Erreur (<strong>critique</strong>) sur la requête : <i>" . $requete . "</i> (" . ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . " : " . ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . ")");
				$pb_maj = 'yes';
				break;
			case "1091" :
				// Déjà supprimé : pas de problème
				$retour = "";
				break;
			default :
				$retour = msj_erreur("Erreur sur la requête : <i>" . $requete . "</i> (" . ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . " : " . ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . ")");
				$pb_maj = 'yes';
				break;
		}
	}
	return $retour;
}

require_once dirname(__FILE__) . '/../fixtures/config/connect.test.inc.php';
$link = ($GLOBALS["mysqli"] = mysqli_connect($GLOBALS['dbHost'],  $GLOBALS['dbUser'],  $GLOBALS['dbPass']));
((bool)mysqli_query($GLOBALS["mysqli"], "USE ".$GLOBALS['dbDb']));
$result = '';
require_once dirname(__FILE__) . '/../../utilitaires/update_functions.php';
require_once dirname(__FILE__) . '/../../lib/mysql.inc';
require_once(dirname(__FILE__). '/../../lib/settings.inc');
require_once(dirname(__FILE__). '/../../lib/share.inc.php');
require_once(dirname(__FILE__). '/../../lib/share-html.inc.php');
require_once dirname(__FILE__) . '/../../utilitaires/updates/155_to_160.inc.php';
require_once dirname(__FILE__) . '/../../utilitaires/updates/160_to_161.inc.php';
// Remplace les sauts de ligne html <br> par \n dans le texte
$result=preg_replace("#<br>#","\n",$result);
$result=preg_replace("#<br/>#","\n",$result);
     
// Supprime les éventuelles balises html et php
$result=strip_tags($result);

// Retourne le texte traité
echo $result; 
