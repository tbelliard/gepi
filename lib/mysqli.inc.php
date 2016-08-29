<?php
/**
 * Initialisation de mysqli
 * 
 * 

 * 
 * Include this file after defining the following variables:
 * - $dbHost = The hostname of the database server
 * - $dbUser = The username to use when connecting to the database
 * - $dbPass = The database account password
 * - $dbDb = The database name.
 * - Including this file connects you to the database, or exits on error
 * 
 */

// Etablir la connexion à la base

//echo 'mysql:host='.$dbHost.';port=".$dbPort.";dbname='.$dbDb.'<br />';

if (isset($utiliser_pdo) AND $utiliser_pdo == 'on') {
	// On utilise le module pdo de php pour entrer en contact avec la base
	//  $cnx = new PDO('mysql:host='.$dbHost.';dbname='.$dbDb, $dbUser, $dbPass);
	if(isset($dbPort)) {
		$cnx = new PDO('mysql:host='.$dbHost.';dbname='.$dbDb.';port='.$dbPort, $dbUser, $dbPass);
	}
	else {
		$cnx = new PDO('mysql:host='.$dbHost.';dbname='.$dbDb, $dbUser, $dbPass);
	}
}

if (!isset($db_nopersist) || $db_nopersist) {
	if(isset($dbPort)) {
		$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDb, $dbPort);
	}
	else {
		$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDb);
	}
}
else {
	if(isset($dbPort)) {
		$mysqli = new mysqli("p:".$dbHost, $dbUser, $dbPass, $dbDb, $dbPort);
	}
	else {
		$mysqli = new mysqli("p:".$dbHost, $dbUser, $dbPass, $dbDb);
	}
}

if ($mysqli->connect_errno) {
    printf("Echec lors de la connexion à MySQL : (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
    exit();
}

/* Modification du jeu de résultats en utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", $mysqli->error);
}

// Fonctions GEPI

/**
 * Execute an SQL query. 
 * 
 * Retourne FALSE en cas d'échec. 
 * Pour des requêtes SELECT, SHOW, DESCRIBE ou EXPLAIN réussies, mysqli_query() 
 * retournera un objet mysqli_result. 
 * Pour les autres types de requêtes ayant réussies, mysqli_query() retournera TRUE.
 * 
 * @param type $sql
 * @return type 
 */
function sqli_query ($sql)
{
    global $mysqli;
    
    $r = mysqli_query($mysqli, $sql);
    return $r;
}

/**
 * Execute an SQL query which should return a single non-negative number value.
 * 
 * This is a lightweight alternative to sql_query, good for use with count(*)
 * and similar queries. It returns -1 on error or if the query did not return
 * exactly one value, so error checking is somewhat limited.
 * It also returns -1 if the query returns a single NULL value, such as from
 * a MIN or MAX aggregate function applied over no rows.
 * @param type $sql
 * @return type 
 */
function sqli_query1 ($sql)
{
    global $mysqli;
    
    $resultat = mysqli_query($mysqli, $sql);
    if (!$resultat) return (-1);
    if ($resultat->num_rows != 1) return (-1);
    if ($resultat->field_count != 1) return (-1);
    $ligne1 = $resultat->fetch_row();
    $result = $ligne1[0];
    if ($result == "") return (-1);
    
    $resultat->close();
    return $result;
}

/**
 *  Return a row from a result. The first row is 0.
* The row is returned as an array with index 0=first column, etc.
* When called with i >= number of rows in the result, cleans up from
* the query and returns 0.
* Typical usage: $i = 0; while ((a = sql_row($r, $i++))) { ... }
 */
function sqli_row ($r, $i)
{
    global $mysqli;

    if ($i >= $r->num_rows) {
        $r->free();
        return 0;
    }
    $r->data_seek($i);
    return $r->fetch_row();
}

/** 
 * Retourne le nombre de lignes d'un objet mysqli.
 * @param type $r
 * @return type 
 */
function sqli_count ($r)
{
    return ($r->num_rows);
}

// Le mode strict de mysql 5.7 pose des problèmes avec certaine valeurs par défaut de certains champs (date à 0000-00-00 00:00:00 par exemple)
// Le mode forcé par défaut dans Gepi permet de revenir au comportement mysql 5.6
// Voir http://dev.mysql.com/doc/refman/5.6/en/sql-mode.html et http://dev.mysql.com/doc/refman/5.7/en/sql-mode.html
// Il est possible de définir un autre mode via une variable $set_mode_mysql à déclarer dans le secure/connect.inc.php
if(!isset($set_mode_mysql)) {
	sqli_query("SET MODE='NO_ENGINE_SUBSTITUTION'");
}
else {
	sqli_query("SET MODE='$set_mode_mysql'");
}
?>
