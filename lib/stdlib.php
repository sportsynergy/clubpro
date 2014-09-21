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
* - setdefault()
* - nvl()
* - ov()
* - pv()
* - o()
* - p()
* - db_query_loop()
* - db_listbox()
* - strip_querystring()
* - get_refererwithq()
* - get_referer()
* - me()
* - mewithq()
* - qualified_me()
* - qualified_mewithq()
* - match_referer()
* - redirect()
* - read_template()
* - checked()
* - frmchecked()
* Classes list:
*/
/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 15:46:27 -0500 (Fri, 26 May 2006) $

 stdlib.php (c) 2000 Ying Zhang (ying@zippydesign.com)
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
function setdefault(&$var, $default = "") {

    /* if $var is undefined, set it to $default.  otherwise leave it alone */
    
    if (!isset($var)) {
        $var = $default;
    }
}
function nvl(&$var, $default = "") {

    /* if $var is undefined, return $default, otherwise return $var */
    return isset($var) ? $var : $default;
}
function ov(&$var) {

    /* returns $var with the HTML characters (like "<", ">", etc.) properly quoted,
     * or if $var is undefined, will return an empty string.  note this function
     * must be called with a variable, for normal strings or functions use o() */
    return isset($var) ? htmlSpecialChars(stripslashes($var)) : "";
}
function pv(&$var) {

    /* prints $var with the HTML characters (like "<", ">", etc.) properly quoted,
     * or if $var is undefined, will print an empty string.  note this function
     * must be called with a variable, for normal strings or functions use p() */
    echo isset($var) ? htmlSpecialChars(stripslashes($var)) : "";
}
function o($var) {

    /* returns $var with HTML characters (like "<", ">", etc.) properly quoted,
     * or if $var is empty, will return an empty string. */
    return empty($var) ? "" : htmlSpecialChars(stripslashes($var));
}
function p($var) {

    /* prints $var with HTML characters (like "<", ">", etc.) properly quoted,
     * or if $var is empty, will print an empty string. */
    echo empty($var) ? "" : htmlSpecialChars(stripslashes($var));
}
function db_query_loop($query, $prefix, $suffix, $found_str, $default = "") {

    /* this is an internal function and normally isn't called by the user.  it
     * loops through the results of a select query $query and prints HTML
     * around it, for use by things like listboxes and radio selections
     *
     * NOTE: this function uses dblib.php */
    $output = "";
    $result = db_query($query);
    while (list($val, $label) = db_fetch_row($result)) {
        
        if (is_array($default)) $selected = empty($default[$val]) ? "" : $found_str;
        else $selected = $val == $default ? $found_str : "";
        $output.= "$prefix value='$val' $selected>$label$suffix";
    }
    return $output;
}
function db_listbox($query, $default = "", $suffix = "\n") {

    /* generate the <option> statements for a <select> listbox, based on the
     * results of a SELECT query ($query).  any results that match $default
     * are pre-selected, $default can be a string or an array in the case of
     * multi-select listboxes.  $suffix is printed at the end of each <option>
     * statement, and normally is just a line break */
    return db_query_loop($query, "<option", $suffix, "selected", $default);
}
function strip_querystring($url) {

    /* takes a URL and returns it without the querystring portion */
    
    if ($commapos = strpos($url, '?')) {
        return substr($url, 0, $commapos);
    } else {
        return $url;
    }
}
function get_refererwithq() {

    /* returns the URL of the HTTP_REFERER, less the querystring portion */
    $HTTP_REFERER = getenv("HTTP_REFERER");
    return nvl($HTTP_REFERER);
}
function get_referer() {

    /* returns the URL of the HTTP_REFERER, less the querystring portion */
    $HTTP_REFERER = getenv("HTTP_REFERER");
    return strip_querystring(nvl($HTTP_REFERER));
}
function me() {

    /* returns the name of the current script, without the querystring portion.
     * this function is necessary because PHP_SELF and REQUEST_URI and PATH_INFO
     * return different things depending on a lot of things like your OS, Web
     * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.) */
    $me = "";
	
    if (getenv("REQUEST_URI")) {
        $me = getenv("REQUEST_URI");
    } elseif (getenv("PATH_INFO")) {
        $me = getenv("PATH_INFO");
    } 
    return strip_querystring($me);
}
function mewithq() {

    /* returns the name of the current script, with the querystring portion.
     * this function is necessary because PHP_SELF and REQUEST_URI and PATH_INFO
     * return different things depending on a lot of things like your OS, Web
     * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.) */
    $me = "";
	
    if (getenv("REQUEST_URI")) {
        $me = getenv("REQUEST_URI");
    } elseif (getenv("PATH_INFO")) {
        $me = getenv("PATH_INFO");
    } 

    return $me;
}
function qualified_me() {

    /* like me() but returns a fully URL */
    $HTTPS = getenv("HTTPS");
    $SERVER_PROTOCOL = getenv("SERVER_PROTOCOL");
    $HTTP_HOST = getenv("HTTP_HOST");
    $protocol = (isset($HTTPS) && $HTTPS == "on") ? "https://" : "http://";
    $url_prefix = "$protocol$HTTP_HOST";
    return $url_prefix . me();
}
function qualified_mewithq() {

    /* like me() but returns a fully URL */
    $HTTPS = getenv("HTTPS");
    $SERVER_PROTOCOL = getenv("SERVER_PROTOCOL");
    $HTTP_HOST = getenv("HTTP_HOST");
    $protocol = (isset($HTTPS) && $HTTPS == "on") ? "https://" : "http://";
    $url_prefix = "$protocol$HTTP_HOST";
    return $url_prefix . mewithq();
}
function match_referer($good_referer = "") {

    /* returns true if the referer is the same as the good_referer.  If
     * good_refer is not specified, use qualified_me as the good_referer */
    
    if ($good_referer == "") {
        $good_referer = qualified_me();
    }
    return $good_referer == get_referer();
}
function redirect($url, $message = "", $delay = 0) {

    /* redirects to a new URL using meta tags */
    echo "<meta http-equiv='Refresh' content='$delay; url=$url'>";
    
    if (!empty($message)) echo "<div style='font-family: Arial, Sans-serif; font-size: 12pt;' align=center>$message</div>";
    die;
}
function read_template($filename, &$var) {

    /* return a (big) string containing the contents of a template file with all
     * the variables interpolated.  all the variables must be in the $var[] array or
     * object (whatever you decide to use).
     *
     * WARNING: do not use this on big files!! */
    $temp = str_replace("\\", "\\\\", implode(file($filename) , ""));
    $temp = str_replace('"', '\"', $temp);
    eval("\$template = \"$temp\";");
    return $template;
}
function checked(&$var, $set_value = 1, $unset_value = 0) {

    /* if variable is set, set it to the set_value otherwise set it to the
     * unset_value.  used to handle checkboxes when you are expecting them from
     * a form */
    
    if (empty($var)) {
        $var = $unset_value;
    } else {
        $var = $set_value;
    }
}
function frmchecked(&$var, $true_value = "checked", $false_value = "") {

    /* prints the word "checked" if a variable is true, otherwise prints nothing,
     * used for printing the word "checked" in a checkbox form input */
    
    if ($var) {
        echo $true_value;
    } else {
        echo $false_value;
    }
}
?>