#!/usr/bin/php 
<?php

require_once dirname(__FILE__) . '/../fixtures/config/connect.test.inc.php';

$link = mysql_connect($GLOBALS['dbHost'], $GLOBALS['dbUser'], $GLOBALS['dbPass']);
mysql_select_db($GLOBALS['dbDb']);
$fd = fopen(dirname(__FILE__) ."/../../sql/structure_gepi.sql", "r");
if (!$fd) {
	echo "Erreur : fichier sql/structure_gepi.sql non trouve\n";
	die;
}
$result_ok = 'yes';
while (!feof($fd)) {
	$query=" ";
	while ((substr($query,-1)!=";") && (!feof($fd))) {
		$t_query = fgets($fd, 8000);
		if (substr($t_query,0,3)!="-- ") $query.=$t_query;
		$query = trim($query); 
	}
	if ($query!="") {
		$reg = mysql_query($query);
		if (!$reg) {
			echo "ERROR : '$query' : \n";
			echo "Erreur retournée : ".mysql_error()."\n";
			$result_ok = 'no';
		}
	}
}
fclose($fd);

if ($result_ok == 'yes') {
	$fd = fopen(dirname(__FILE__) ."/../../sql/data_gepi.sql", "r");
	if (!$fd) {
		echo "Erreur : fichier sql/data_gepi.sql non trouve\n";
		die;
	}
	while (!feof($fd)) {
		$query = fgets($fd, 5000);
		$query = trim($query);
		if((substr($query,-1)==";")&&(substr($query,0,3)!="-- ")) {
			$reg = mysql_query($query);
			if (!$reg) {
				echo "ERROR : '$query' \n";
				echo "Erreur retournée : ".mysql_error()."\n";
				$result_ok = 'no';
			}
		}
	}
	fclose($fd);
}

if ($result_ok == 'yes') {
	echo "ok \n";
} else {
	echo "error\n";
}

