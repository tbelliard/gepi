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


if (isset($utiliser_pdo) AND $utiliser_pdo == 'on') {
  // On utilise le module pdo de php pour entrer en contact avec la base
  $cnx = new PDO('mysql:host='.$dbHost.';dbname='.$dbDb, $dbUser, $dbPass);

}

// manque la gestion des connexion 
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDb);
if ($mysqli->connect_errno) {
    printf("Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
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
function sql_query ($sql)
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
function sql_query1 ($sql)
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



?>
