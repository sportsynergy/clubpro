<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
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
* Classes list:
* - object
*/
/* define a generic object */

class object {
};

/* define database error handling behavior, since we are in development stages
 * we will turn on all the debugging messages to help us troubleshoot */
$DB_DEBUG = true;
$DB_DIE_ON_FAIL = true;

/*
 * Here is how debug levels work.
 * 1 = INFO
 * 2 = WARN
 * 3 = ERROR
*/
$APP_DEBUG = 1;
$MAIL_DEBUG = true;

/* start up the sessions, to keep things clean and manageable we will just
 * use one array called SESSION to store our persistent variables.   */
session_start();

/* reset the configuration cache if switched from another instance */

if (isset($_SESSION["CFG"]["wwwroot"])) {

    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pathArray = explode("/", $scriptName);
    
    if ("$pathArray[1]/" != $_SESSION["CFG"]["wwwroot"]) {

        unset($_SESSION["CFG"]);
    }
}

/* initialize the SESSION CFG variable if necessary */

if (!isset($_SESSION["CFG"])) {


    
        $_SESSION["CFG"]["wwwroot"] = "/clubpro";
        $_SESSION["CFG"]["imagedir"] = "/clubpro/images";
        $_SESSION["CFG"]["dirroot"] = "/Users/adampreston/Repository/clubpro";
        $_SESSION["CFG"]["templatedir"] = "/Users/adampreston/Repository/clubpro/templates";
        $_SESSION["CFG"]["libdir"] = "/Users/adampreston/Repository/clubpro/lib";
        $_SESSION["CFG"]["wordlist"] = "/Users/adampreston/Repository/clubpro/lib/wordlist.txt";
        $_SESSION["CFG"]["includedir"] = "/Users/adampreston/Repository/clubpro/includes";
        $_SESSION["CFG"]["dns"] = "localhost";
        $_SESSION["CFG"]["support"] = "support@sportsynergy.net";
        $_SESSION["CFG"]["logFile"] = "/Users/a904528/Logs/SystemOut.log";
        $_SESSION["CFG"]["emailhost"] = "https://api.postageapp.com";
   
}

/* load up standard libraries */
require ($_SESSION["CFG"]["libdir"] . "/stdlib.php");
require ($_SESSION["CFG"]["libdir"] . "/dblib.php");
require ($_SESSION["CFG"]["libdir"] . "/applicationlib.php");
require ($_SESSION["CFG"]["includedir"] . "/include_phpAjaxTags.php");

/* setup some global variables */
$ME = qualified_me();
$MEWQ = qualified_mewithq();
$dbhost = "localhost";
$dbname = "clubpro_demo";
$dbuser = "clubpro_main";
$dbpass = "clubpro_main";

/* connect to the database */
db_connect($dbhost, $dbname, $dbuser, $dbpass);
?>