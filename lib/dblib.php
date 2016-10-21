<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
* Version 2.1, February 1999
*
* <one line to give the library's name and a brief idea of what it does.>
*
* Copyright (C) 2001~2012 Adam Preston
* 
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* $Id:$
*/

/**
 * Class and Function List:
* Function list:
* - db_connect()
* - db_disconnect()
* - db_query()
* - db_fetch_array()
* - db_fetch_row()
* - db_fetch_object()
* - db_num_rows()
* - db_affected_rows()
* - db_insert_id()
* - db_free_result()
* - db_num_fields()
* - db_field_name()
* - db_data_seek()
* Classes list:
*/
/*
 * $LastChangedRevision: 2 $
* $LastChangedBy: Adam Preston $
* $LastChangedDate: 2006-05-26 15:46:27 -0500 (Fri, 26 May 2006) $



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

if (!isset($DB_DIE_ON_FAIL)) {
	$DB_DIE_ON_FAIL = true;
}

if (!isset($DB_DEBUG)) {
	$DB_DEBUG = false;
}

/**
 * connect to the database $dbname on $dbhost with the user/password pair
 * $dbuser and $dbpass.
 *
 * @param unknown_type $dbhost
 * @param unknown_type $dbname
 * @param unknown_type $dbuser
 * @param unknown_type $dbpass
 */
function db_connect($dbhost, $dbname, $dbuser, $dbpass) {

	global $DB_DIE_ON_FAIL, $DB_DEBUG;

	if (!$dbh = mysqli_connect($dbhost, $dbuser, $dbpass)) {

		if ($DB_DEBUG) {
			echo "<h2>Can't connect to $dbhost as $dbuser</h2>";
			echo "<p><b>MySQL Error</b>: ", mysqli_connect_error();
		} else {
			echo "<h2>Database error encountered</h2>";
		}

		if ($DB_DIE_ON_FAIL) {
			echo "<p>This script cannot continue, terminating.";
			die();
		}
	}

	if (!mysqli_select_db($dbh,$dbname)) {

		if ($DB_DEBUG) {
			echo "<h2>Can't select database $dbname</h2>";
			echo "<p><b>MySQL Error</b>: ", mysqli_error($dbh);
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

/**
 * disconnect from the database, we normally don't have to call this function
 * because PHP will handle it
 */
function db_disconnect() {
	mysql_close();
}

/**
 * run the query $query against the current database.  if $debug is true, then
 * we will just display the query on screen.  if $die_on_debug is true, and
 * $debug is true, then we will stop the script after printing he debug message,
 * otherwise we will run the query.  if $silent is true then we will surpress
 * all error messages, otherwise we will print out that a database error has
 * occurred
 *
 * @param unknown_type $query
 * @param unknown_type $debug
 * @param unknown_type $die_on_debug
 * @param unknown_type $silent
 */
function db_query($query, $debug = false, $die_on_debug = true, $silent = false) {

	global $DB_DIE_ON_FAIL, $DB_DEBUG, $dbh;

	if ($debug) {
		echo "<pre>" . htmlspecialchars($query) . "</pre>";
		if ($die_on_debug) {
			die;
		}
	}
	$qid = mysqli_query($dbh, $query);

	if (!$qid && !$silent) {

		if ($DB_DEBUG) {
			echo "<h2>Can't execute query</h2>";
			echo "<pre>" . htmlspecialchars($query) . "</pre>";
			echo "<p><b>MySQL Error</b>: ", mysqli_error($dbh);
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

/**
 * GRAB THE NEXT ROW FROM THE QUERY RESULT IDENTIFIER $QID, AND RETURN IT
 * AS AN ASSOCIATIVE ARRAY.  IF THERE ARE NO MORE RESULTS, RETURN FALSE
 * 
 * @param unknown_type $qid
 */
function db_fetch_array($qid) {
	return mysqli_fetch_array($qid);
}

/**
 * grab the next row from the query result identifier $qid, and return it
 * as an array.  if there are no more results, return FALSE
 * 
 * @param unknown_type $qid
 */
function db_fetch_row($qid) {
	return mysqli_fetch_row($qid);
}

/**
 * grab the next row from the query result identifier $qid, and return it
 * as an object.  if there are no more results, return FALSE
 * 
 * @param unknown_type $qid
 */
function db_fetch_object($qid) {
	return mysqli_fetch_object($qid);
}

/**
 * return the number of records (rows) returned from the SELECT query with
 * the query result identifier $qid.
 * 
 * @param unknown_type $qid
 */
function db_num_rows($qid) {
	return mysqli_num_rows($qid);
}

/**
 * return the number of rows affected by the last INSERT, UPDATE, or DELETE
 * query
 */
function db_affected_rows() {
	return mysql_affected_rows();
}

/**
 * if you just INSERTed a new row into a table with an autonumber, call this
 * function to give you the ID of the new autonumber value
 */
function db_insert_id() {
	return mysql_insert_id();
}

/**
 * free up the resources used by the query result identifier $qid
 * 
 * @param unknown_type $qid
 */
function db_free_result($qid) {
	mysql_free_result($qid);
}

/**
 * return the number of fields returned from the SELECT query with the
 * identifier $qid
 * 
 * @param unknown_type $qid
 */
function db_num_fields($qid) {
	return mysql_num_fields($qid);
}

/**
 * return the name of the field number $fieldno returned from the SELECT query
 * with the identifier $qid 
 * 
 * @param unknown_type $qid
 * @param unknown_type $fieldno
 */
function db_field_name($qid, $fieldno) {
	return mysql_field_name($qid, $fieldno);
}

/**
 * move the database cursor to row $row on the SELECT query with the identifier
 * $qid
 * 
 * @param unknown_type $qid
 * @param unknown_type $row
 */
function db_data_seek($qid, $row) {
	if (db_num_rows($qid)) {
		return mysqli_data_seek($qid, $row);
	}
}
?>