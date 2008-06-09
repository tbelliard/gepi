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

/*/ Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};*/

 // Import de la classe RSSFeed
require('RSSFeed/RSSFeed.class.php');

 // Création des entêtes du flux RSS
//$oRssFeed = new RSSFeed('utf-8');
$oRssFeed = new RSSFeed('ISO-8859-1');
$oRssFeed->setProtectString(true);
$oRssFeed->setTitle('DEVOIRS');
$oRssFeed->setDescription('Les devoirs de la semaine');
$oRssFeed->setLink('http://localhost/'.$gepiPath);
$oRssFeed->setPubDate('2007-12-31');
$oRssFeed->setLastBuildDate(date('Y-m-d'));
$oRssFeed->setWebMaster('julien.jocal@ac-bordeaux.fr','JJOCAL');
$oRssFeed->setManagingEditor('julien.jocal@ac-bordeaux.fr','JJOCAL');
$oRssFeed->setImage($gepiPath.'/favicon.ico', 'GEPI', 'http://localhost');
$oRssFeed->setCopyright('(L) - GEPI 151');
$oRssFeed->setGenerator('Généré par RSSFeed Class de Hugo "Emacs" HAMON - http://www.apprendre-php.com');
$oRssFeed->setLanguage('fr');

 // Ajout des news au flux
for($a = 0; $a < 3 ; $a++)
{
	// Récupération de l'email
	$sEmail = 'julien.jocal@ac-bordeaux.fr';
	$oRssItem = new RSSFeedItem();
	$oRssItem->setTitle('Histoire-géographie '.date("d-m-Y h:i", 1212962400));
	$oRssItem->setDescription(htmlentities('<a href="#"></a> </description>'));
	$oRssItem->setLink('http://localhost'.$gepiPath.'/class_php/test_class_user.php?id=1');
	$oRssItem->setGuid('http://localhost'.$gepiPath.'/class_php/test_class_user.php?id=1', true);
	if(!empty($sEmail))
	{
		$oRssItem->setAuthor($sEmail, 'JJOCAL');
	}
	$oRssItem->setPubDate('2008-06-08 20:00:00');
	$oRssFeed->appendItem($oRssItem);
	$oRssItem = null;
}
 // Sauvegarde du flux RSS
$oRssFeed->save('rss-news.xml');
// Affichage sur la sortie standard
header('Content-Type: text/xml; charset=ISO-8859-1');
$oRssFeed->display();
?>