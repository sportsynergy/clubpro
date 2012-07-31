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

include ("../application.php");
require_login();
$DOC_TITLE = "Add Court";
$courttypes = getCourtTypes();

if (match_referer() && isset($_POST['submitme'])) {
    
		$frm = $_POST;
		
		$courttypeid = $frm["courttypeid"];
		$courtname = $frm["courtname"];
		$siteid = $frm["siteid"];
		
        insert_court($courttypeid, $courtname, $siteid);

        $wwwroot = $_SESSION["CFG"]["wwwroot"];
        header("Location: $wwwroot/admin/site_info.php?siteid=$siteid");
    
}


include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/add_court_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function getCourtTypes(){
	
	$query = "SELECT courttypeid, courttypename FROM tblCourtType";
	return db_query($query);
}

function insert_court($courttypeid, $courtname, $siteid, $opentime, $closetime) {

		if( isDebugEnabled(1) ) logMessage("add_court: Inserting court for site $siteid");

		// Look up club id
		$query = "select clubid from tblClubSites where siteid = $siteid";
		$result = db_query($query);
		
		$clubid = mysql_result($result, 0);

		// Add Court
		//courtid	courttypeid	clubid	courtname	enable	siteid	lastmodified
        $query = "INSERT INTO tblCourts (
	                courttypeid, clubid, courtname, siteid
	                ) VALUES (
	                           '$courttypeid'
	                          ,'$clubid'
	                          ,'$courtname'
							  , '$siteid')";
    

    // run the query on the database
    $result = db_query($query);

	// Add Court Hours
	//id	dayid	courtid	opentime	closetime	hourstart	duration	lastmodified
	for($i=0; $i<7; ++$i){
		 $query = "INSERT INTO tblCourtHours (
		                dayid, courtid, opentime, closetime, hourstart, duration
		                ) VALUES (
		                           '$i'
								,'$court'
		                          ,'$clubid'
		                          ,'$courtname'
								  , '$siteid')";


	    // run the query on the database
	    $result = db_query($query);
	}
	

}
?>