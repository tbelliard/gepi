<?php

// Si l'api mysql est désactivée ou absente
if (!function_exists("mysqli_connect")) require("mysql2mysqli.php");

/**
 * Initialisation de mysql
 * 
 * 
 * mysql.inc - Simple PHP database support for MySQL.
 * 
 * Include this file after defining the following variables:
 * - $dbHost = The hostname of the database server
 * - $dbUser = The username to use when connecting to the database
 * - $dbPass = The database account password
 * - $dbDb = The database name.
 * - Including this file connects you to the database, or exits on error
 * 
 */

mysqli_query($GLOBALS["mysqli"], "SET NAMES UTF8");

/** Free a results handle. You need not call this if you call sql_row or
 * sql_row_keyed until the row returns 0, since sql_row frees the results
 * handle when you finish reading the rows.
 *
 * @param type $r 
 */
function sql_free ($r)
{
    ((mysqli_free_result($r) || (is_object($r) && (get_class($r) == "mysqli_result"))) ? true : false);
}

/**
 * Execute a non-SELECT SQL command (insert/update/delete).
 * 
 * Returns the number of tuples affected if OK (a number >= 0).
 * Returns -1 on error; use sql_error to get the error message.
 * @param type $sql
 * @return int 
 */
function sql_command ($sql)
{
    if (mysqli_query($GLOBALS["mysqli"], $sql)) return mysqli_affected_rows($GLOBALS["mysqli"]);
    return -1;
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
    $r = mysqli_query($GLOBALS["mysqli"], $sql);
    if (! $r) return -1;
    if (mysqli_num_rows($r) != 1 || (($___mysqli_tmp = mysqli_num_fields($r)) ? $___mysqli_tmp : false) != 1
        || ($result = old_mysql_result($r, 0, 0)) == "") $result = -1;
    ((mysqli_free_result($r) || (is_object($r) && (get_class($r) == "mysqli_result"))) ? true : false);
    return $result;
}


/**
 * Execute an SQL query. 
 * 
 * Returns a database-dependent result handle,
 * which should be passed back to sql_row or sql_row_keyed to get the results.
 * Returns 0 on error; use sql_error to get the error message.
 * @param type $sql
 * @return type 
 */
function sql_query ($sql)
{
    $r = mysqli_query($GLOBALS["mysqli"], $sql);
    return $r;
}

/**
 *  Return a row from a result. The first row is 0.
* The row is returned as an array with index 0=first column, etc.
* When called with i >= number of rows in the result, cleans up from
* the query and returns 0.
* Typical usage: $i = 0; while ((a = sql_row($r, $i++))) { ... }
 */
function sql_row ($r, $i)
{
    if ($i >= mysqli_num_rows($r))
    {
        ((mysqli_free_result($r) || (is_object($r) && (get_class($r) == "mysqli_result"))) ? true : false);
        return 0;
    }
    mysqli_data_seek($r,  $i);
    return mysqli_fetch_row($r);
}

/** 
 * Return a row from a result as an associative array keyed by field name.
 * 
* The first row is 0.
* This is actually upward compatible with sql_row since the underlying
* routing also stores the data under number indexes.
* When called with i >= number of rows in the result, cleans up from
* the query and returns 0.
 */
function sql_row_keyed ($r, $i)
{
    if ($i >= mysqli_num_rows($r))
    {
        ((mysqli_free_result($r) || (is_object($r) && (get_class($r) == "mysqli_result"))) ? true : false);
        return 0;
    }
    mysqli_data_seek($r,  $i);
    return mysqli_fetch_array($r);
}

/** 
 * Return the number of rows returned by a result handle from sql_query.
 * @param type $r
 * @return type 
 */
function sql_count ($r)
{
    return mysqli_num_rows($r);
}

/**
 *Return the value of an autoincrement field from the last insert.
 * 
 * Must be called right after an insert on that table!
 * @param type $table
 * @param type $field
 * @return type 
 */
function sql_insert_id($table, $field)
{
    return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
}

/** Return the text of the last error message.
 *
 * @return type 
 */
function sql_error()
{
    return ((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
}

/**
 * Begin a transaction, if the database supports it. This is used to
 * 
 * improve PostgreSQL performance for multiple insert/delete/updates.
 * There is no rollback support, since MySQL doesn't support it.
 */
function sql_begin()
{
}

/** 
 * Commit (end) a transaction. See sql_begin().
 */
function sql_commit()
{
}

/**
 * Acquire a mutual-exclusion lock on the named table.
 * 
 * For portability:
 * - This will not lock out SELECTs.
 * - It may lock out DELETE/UPDATE/INSERT or not, depending on the implementation.
 * - It will lock out other callers of this routine with the same name argument.
 * - It may timeout in 20 seconds and return 0, or may wait forever.
 * - It returns 1 when the lock has been acquired.
 * - Caller must release the lock with sql_mutex_unlock().
 * - Caller must not have more than one mutex at any time.
 * - Do not mix this with sql_begin()/sql_end() calls
 * 
 * In MySQL, we avoid table locks, and use low-level locks instead.
 * 
 * @global int $sql_mutex_shutdown_registered
 * @global type $sql_mutex_unlock_name
 * @param type $name
 * @return type 
 */
function sql_mutex_lock($name)
{
    global $sql_mutex_shutdown_registered, $sql_mutex_unlock_name;
    if (!sql_query1("SELECT GET_LOCK('$name', 20)")) return 0;
    $sql_mutex_unlock_name = $name;
    if (empty($sql_mutex_shutdown_registered))
    {
        register_shutdown_function("sql_mutex_cleanup");
        $sql_mutex_shutdown_registered = 1;
    }
    return 1;
}

/** 
 * Release a mutual-exclusion lock on the named table. See sql_mutex_unlock.
 *
 * @global string $sql_mutex_unlock_name
 * @param type $name 
 */
function sql_mutex_unlock($name)
{
    global $sql_mutex_unlock_name;
    sql_query1("SELECT RELEASE_LOCK('$name')");
    $sql_mutex_unlock_name = "";
}

/** 
 * Shutdown function to clean up a forgotten lock. For internal use only.
 *
 * @global int $sql_mutex_shutdown_registered
 * @global string $sql_mutex_unlock_name 
 */
function sql_mutex_cleanup()
{
    global $sql_mutex_shutdown_registered, $sql_mutex_unlock_name;
    if (!empty($sql_mutex_unlock_name))
    {
        sql_mutex_unlock($sql_mutex_unlock_name);
        $sql_mutex_unlock_name = "";
    }
}


/** 
 * Return a string identifying the database version:
 *
 * @return type 
 */
function sql_version()
{
    $r = sql_query("select version()");
    $v = sql_row($r, 0);
    sql_free($r);
    return "MySQL $v[0]";
}


/** 
 * Generate non-standard SQL for LIMIT clauses:
 * @param type $count
 * @param type $offset
 * @return type 
 */
function sql_syntax_limit($count, $offset)
{
    return " LIMIT $offset,$count ";
}

/** 
 * Generate non-standard SQL to output a TIMESTAMP as a Unix-time:
 *
 * @param type $fieldname
 * @return type 
 */
function sql_syntax_timestamp_to_unix($fieldname)
{
    return " UNIX_TIMESTAMP($fieldname) ";
}

/**
 * Generate non-standard SQL to match a string anywhere in a field's valuein a case insensitive manner.
 * 
 * $s is the un-escaped/un-slashed string.
 * In MySQL, REGEXP seems to be case sensitive, so use LIKE instead. But this
 * requires quoting of % and _ in addition to the usual.
 * @param type $fieldname
 * @param type $s
 * @return type 
 */
function sql_syntax_caseless_contains($fieldname, $s)
{
    $s = str_replace("\\", "\\\\", $s);
    $s = str_replace("%", "\\%", $s);
    $s = str_replace("_", "\\_", $s);
    $s = str_replace("'", "''", $s);
    return " $fieldname LIKE '%$s%' ";
}

?>
