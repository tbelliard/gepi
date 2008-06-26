<?php

/**
 * @version $Id$
 *
 * @copyright 2008
 *
 */

$niveau_arbo = 1;
// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../cahier_texte/fonctions_cdt.inc.php");

// Ici, il faudra vérifier les droits et les autorisations sur les flux
$type_rss = isset($_GET["type"]) ? $_GET["type"] : NULL;
$eleve_l = isset($_GET["ele_l"]) ? $_GET["ele_l"] : NULL;
$uri = isset($_GET["uri"]) ? $_GET["uri"] : NULL;

// On vérifie si la table des uri existe (si elle existe, elle est forcément remplie
$test_table = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'rss_users'"));
	if ($test_table == 0) {

		die();

	}else{

		// On peut alors vérifier si l'uri demandée est la bonne
		$test_uri = mysql_num_rows(mysql_query("SELECT id FROM rss_users WHERE user_login = '".$eleve_l."' AND user_uri = '".$uri."' LIMIT 1"));

		if ($test_uri == 1) {
			// C'est bon, on peut générer les réponses
		}else{

			die();

		}

	}

// =========================== Cahier de textes =================================
if ($type_rss == "cdt") {
 	$items = retourneDevoirs($eleve_l);
 	function get_prof_login($prof_l){
 		/*
 		 * permet de construire le nom du prof avec la bonne civilité
 		*/
 		$sql = "SELECT nom, civilite FROM utilisateurs WHERE login = '".$prof_l."' AND statut = 'professeur'";
 		$query = mysql_query($sql);
 		$test = mysql_num_rows($query);
 		if ($test == 1) {
 			// c'est bon, on construit son nom
 			$prof = mysql_fetch_array($query);
 			if (isset($prof["civilite"])) {
 				$civilite = $prof["civilite"];
 			}else{
			 	$civilite = "";
			}
			$rep = $civilite.'&nbsp;'.$prof["nom"];
 		}else{
			$rep = 'Erreur dans la reconnaissance de l\'enseignant';
		}
		return $rep;
 	}
 	$noms["nom"] = $noms["prenom"] = NULL;
 	$noms = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$eleve_l."' LIMIT 1"));
 	$title_rss = 'Cahier de textes - '.getSettingValue("gepiSchoolName").' ('.getSettingValue("gepiYear").').';
 	$description_rss = 'Les devoirs à faire de '.$noms["nom"].' '.$noms["prenom"];
}
// =========================fin des cahiers de textes ===========================

 // Import de la classe RSSFeed
require('RSSFeed/RSSFeed.class.php');

 // Création des entêtes du flux RSS
//$oRssFeed = new RSSFeed('utf-8');
$oRssFeed = new RSSFeed('ISO-8859-1');
$oRssFeed->setCloud($_SERVER["SERVER_NAME"], $_SERVER["REMOTE_PORT"], $gepiPath, '', 'http');
$oRssFeed->setProtectString(true);
$oRssFeed->setTitle($title_rss);
$oRssFeed->setDescription($description_rss);
$oRssFeed->setLink('http://'.$_SERVER["SERVER_NAME"].$gepiPath);
$oRssFeed->setPubDate('2007-12-31');
$oRssFeed->setLastBuildDate(date('Y-m-d'));
$oRssFeed->setWebMaster(getSettingValue("gepiSchoolEmail"),'ADMIN');
$oRssFeed->setManagingEditor(getSettingValue("gepiSchoolEmail"),'ADMIN');
$oRssFeed->setImage($gepiPath.'/favicon.ico', 'GEPI', 'http://'.$_SERVER["SERVER_NAME"]);
$oRssFeed->setCopyright('(L) - GEPI 151');
$oRssFeed->setGenerator('Généré par RSSFeed Class de Hugo "Emacs" HAMON - http://www.apprendre-php.com');
$oRssFeed->setLanguage('fr');

// Ajout des news au flux
if ($items["cdt_dev"]["count"] != 0) {
	for($a = 0; $a < $items["cdt_dev"]["count"] ; $a++)
	{

		// On récupère des données
		$donnees = get_group($items["cdt_dev"][$a]["id_groupe"]);
		$prof = get_prof_login($items["cdt_dev"][$a]["id_login"]);

		// Récupération de l'email
		$sEmail = getSettingValue("gepiSchoolEmail");
		$oRssItem = new RSSFeedItem();
		$oRssItem->setTitle($donnees["description"].' - Pour le '.date("d-m-Y", $items["cdt_dev"][$a]["date_ct"]));
		$oRssItem->setDescription('-> Travail donné par '.$prof.' : '.$items["cdt_dev"][$a]["contenu"]);
		$oRssItem->setLink('http://'.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
		$oRssItem->setGuid('http://'.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
		if(!empty($sEmail))
		{
			$oRssItem->setAuthor($sEmail, 'ADMIN');
		}
		$oRssItem->setPubDate(date("Y-m-d h:i:s", $items["cdt_dev"][$a]["date_ct"]));
		$oRssFeed->appendItem($oRssItem);
		$oRssItem = null;
	}
}elseif($items["cdt_dev"]["count"] == 0){

	// Récupération de l'email
	$sEmail = getSettingValue("gepiSchoolEmail");
	$oRssItem = new RSSFeedItem();
	$oRssItem->setTitle('Le cahier de textes est vide');
	$oRssItem->setDescription('Rien &agrave; afficher -> Il faut toujours revoir les le&ccedil;ons du jour.');
	$oRssItem->setLink('http://'.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
	$oRssItem->setGuid('http://'.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
	if(!empty($sEmail))
	{
		$oRssItem->setAuthor($sEmail, 'ADMIN');
	}
	$oRssItem->setPubDate(date("Y-m-d h:i:s"));
	$oRssFeed->appendItem($oRssItem);
	$oRssItem = null;

}else{

	// Récupération de l'email
	$sEmail = getSettingValue("gepiSchoolEmail");
	$oRssItem = new RSSFeedItem();
	$oRssItem->setTitle('ERREUR sur le CDT');
	$oRssItem->setDescription('Rien &agrave; afficher -> Il faut toujours apprendre les le&ccedil;ons du jour.');
	$oRssItem->setLink('http://'.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php');
	$oRssItem->setGuid('http://'.$_SERVER["SERVER_NAME"].$gepiPath.'/login.php', true);
	if(!empty($sEmail))
	{
		$oRssItem->setAuthor($sEmail, 'ADMIN');
	}
	$oRssItem->setPubDate(date("Y-m-d h:i:s"));
	$oRssFeed->appendItem($oRssItem);
	$oRssItem = null;

}

 // Sauvegarde du flux RSS
$oRssFeed->save('../temp/rss-news.xml');
// Affichage sur la sortie standard
header('Content-Type: text/xml; charset=ISO-8859-1');
$oRssFeed->display();
?>