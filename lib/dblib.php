<?


/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 13:46:27 -0700 (Fri, 26 May 2006) $



/* dblib.php (c) 2000 Ying Zhang (ying@zippydesign.com)
 *
 * TERMS OF USAGE:
 * This file was written and developed by Ying Zhang (ying@zippydesign.com)
 * for educational and demonstration purposes only.  You are hereby granted the
 * rights to use, modify, and redistribute this file as you like.  The only
 * requirement is that you must retain this notice, without modifications, at
 * the top of your source code.  No warranties or guarantees are expressed or
 * implied. DO NOT use this code in a production environment without
 * understanding the limitations and weaknesses pretaining to or caused by the
 * use of these scripts, directly or indirectly. USE AT YOUR OWN RISK!
 */

if (!isset($DB_DIE_ON_FAIL)) { $DB_DIE_ON_FAIL = true; }
if (!isset($DB_DEBUG)) { $DB_DEBUG = false; }

function db_connect($dbhost, $dbname, $dbuser, $dbpass) {
/* connect to the database $dbname on $dbhost with the user/password pair
 * $dbuser and $dbpass. */

        global $DB_DIE_ON_FAIL, $DB_DEBUG;

        if (! $dbh = mysql_connect($dbhost, $dbuser, $dbpass)) {
                if ($DB_DEBUG) {
                        echo "<h2>Can't connect to $dbhost as $dbuser</h2>";
                        echo "<p><b>MySQL Error</b>: ", mysql_error();
                } else {
                        echo "<h2>Database error encountered</h2>";
                }

                if ($DB_DIE_ON_FAIL) {
                        echo "<p>This script cannot continue, terminating.";
                        die();
                }
        }

        if (! mysql_select_db($dbname)) {
                if ($DB_DEBUG) {
                        echo "<h2>Can't select database $dbname</h2>";
                        echo "<p><b>MySQL Error</b>: ", mysql_error();
                } else {
                        echo "<h2>Database error encountered</h2>";
                }

                if ($DB_DIE_ON_FAIL) {
                        echo "<p>This script cannot continue, terminating.";
                        die();
                }
        }

        return $dbh;
}

function db_disconnect() {
/* disconnect from the database, we normally don't have to call this function
 * because PHP will handle it */

        mysql_close();
}


function db_query($query, $debug=false, $die_on_debug=true, $silent=false) {
/* run the query $query against the current database.  if $debug is true, then
 * we will just display the query on screen.  if $die_on_debug is true, and
 * $debug is true, then we will stop the script after printing he debug message,
 * otherwise we will run the query.  if $silent is true then we will surpress
 * all error messages, otherwise we will print out that a database error has
 * occurred */

        global $DB_DIE_ON_FAIL, $DB_DEBUG;

        if ($debug) {
                echo "<pre>" . htmlspecialchars($query) . "</pre>";

                if ($die_on_debug) die;
        }

        $qid = mysql_query($query);

        if (! $qid && ! $silent) {
                if ($DB_DEBUG) {
                        echo "<h2>Can't execute query</h2>";
                        echo "<pre>" . htmlspecialchars($query) . "</pre>";
                        echo "<p><b>MySQL Error</b>: ", mysql_error();
                } else {
                        echo "<h2>Database error encountered</h2>";
                }

                if ($DB_DIE_ON_FAIL) {
                        echo "<p>This script cannot continue, terminating.";
                        die();
                }
        }

        return $qid;
}

function db_fetch_array($qid) {
/* grab the next row from the query result identifier $qid, and return it
 * as an associative array.  if there are no more results, return FALSE */

        return mysql_fetch_array($qid);
}

function db_fetch_row($qid) {
/* grab the next row from the query result identifier $qid, and return it
 * as an array.  if there are no more results, return FALSE */

        return mysql_fetch_row($qid);
}

function db_fetch_object($qid) {
/* grab the next row from the query result identifier $qid, and return it
 * as an object.  if there are no more results, return FALSE */

        return mysql_fetch_object($qid);
}

function db_num_rows($qid) {
/* return the number of records (rows) returned from the SELECT query with
 * the query result identifier $qid. */

        return mysql_num_rows($qid);
}

function db_affected_rows() {
/* return the number of rows affected by the last INSERT, UPDATE, or DELETE
 * query */

        return mysql_affected_rows();
}

function db_insert_id() {
/* if you just INSERTed a new row into a table with an autonumber, call this
 * function to give you the ID of the new autonumber value */

        return mysql_insert_id();
}

function db_free_result($qid) {
/* free up the resources used by the query result identifier $qid */

        mysql_free_result($qid);
}

function db_num_fields($qid) {
/* return the number of fields returned from the SELECT query with the
 * identifier $qid */

        return mysql_num_fields($qid);
}

function db_field_name($qid, $fieldno) {
/* return the name of the field number $fieldno returned from the SELECT query
 * with the identifier $qid */

        return mysql_field_name($qid, $fieldno);
}

function db_data_seek($qid, $row) {
/* move the database cursor to row $row on the SELECT query with the identifier
 * $qid */

        if (db_num_rows($qid)) { return mysql_data_seek($qid, $row); }
}
?>