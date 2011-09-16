#!/usr/bin/php 
<?php

require_once dirname(__FILE__) . '/fixtures/config/connect.test.inc.php';

$link = mysql_connect($GLOBALS['dbHost'], $GLOBALS['dbUser'], $GLOBALS['dbPass']);
mysql_select_db($GLOBALS['dbDb']);
$result = '';
require dirname(__FILE__) . '/../../utilitaires/updates/155_to_current.inc.php';
// Remplace les sauts de ligne html <br> par \n dans le texte
$result=preg_replace("#<br>#","\n",$result);
$result=preg_replace("#<br/>#","\n",$result);
     
// Supprime les éventuelles balises html et php
$result=strip_tags($result);

// Retourne le texte traité
echo $result; 
