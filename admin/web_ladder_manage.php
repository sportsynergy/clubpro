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
* - validate_form()
* - insert_boxuser()
* Classes list:
*/
include ("../application.php");
require_login();
require_priv("2");

//Set the http variables
$action = $_REQUEST["action"];
$boxid = $_REQUEST["boxid"];

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm);
    
    if (empty($errormsg) && empty($action)) {
        insert_boxuser($frm);
		update_box_enddate($frm);
		
		$noticemsg = "Box League Updated.  Good Job!<br/><br/>";
    }
}

$boxnamequery = "SELECT boxleague.boxname, boxleague.courttypeid, boxleague.enddate, tCSL.name, boxleague.ladderid, autoschedule, ladder_type
                        FROM tblBoxLeagues boxleague
                        left join tblClubSiteLadders tCSL on boxleague.ladderid = tCSL.id
                        WHERE boxid=$boxid";

if(!isset($boxid) ){
    header("Location: $wwwroot/admin/web_ladder_registration.php");
}

// run the query on the database
$result = db_query($boxnamequery);
$boxarray = mysqli_fetch_array($result);

//Set some variables for the form
$DOC_TITLE = "Box League - $boxarray[0] ";
$courttype = $boxarray[1];
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_manage_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($frm) {

    // Make sure that the box user isn't already in a box
    
    if (isDebugEnabled(1)) logMessage("web_ladder_manage.validate_form");
    $errors = new clubpro_obj;
    $msg = "";
    
    if (!empty($frm['boxuser'])) {
        
        $boxUserQuery = "SELECT ladderid  FROM tblBoxLeagues
                            WHERE tblBoxLeagues.boxid = $frm[boxid]";
        
        $boxUserResult = db_query($boxUserQuery);

        $isLadderBox = mysqli_result($boxUserResult, 0);
        
        // if not associated with a ladder, then the user can only be in one box
        if ( !isset($isLadderBox ) ){

            $boxUserQuery = "SELECT userid  FROM tblkpBoxLeagues
                                INNER JOIN tblBoxLeagues on tblkpBoxLeagues.boxid = tblBoxLeagues.boxid
                                WHERE tblkpBoxLeagues.userid = $frm[boxuser]
                                AND tblBoxLeagues.enable=TRUE";

            $boxUserResult = db_query($boxUserQuery); 
            if (mysqli_num_rows($boxUserResult) > 0) {
            
                if (isDebugEnabled(1)) logMessage("\t-> boxuser is not in a box");
                $errors->boxuser = true;
                $msg= "A player cannot be in more than one box at a time.";
            }              

        // just look for leagues for that ladder
        } else {

            $boxUserQuery = "SELECT userid FROM tblkpBoxLeagues
                            INNER JOIN tblBoxLeagues on tblkpBoxLeagues.boxid = tblBoxLeagues.boxid
                            WHERE userid = $frm[boxuser]
                            AND tblBoxLeagues.ladderid=$frm[ladderid]
                            AND tblBoxLeagues.enable=TRUE";
            
            $boxUserResult = db_query($boxUserQuery); 
            if (mysqli_num_rows($boxUserResult) > 0) {
            
                if (isDebugEnabled(1)) logMessage("\t-> boxuser is not in a box");
                $errors->boxuser = true;
                $msg= "A player cannot be in more than one box at a time.";
            } 
             
        }
        
    }
    return $msg;
}


function insert_boxuser(&$frm) {

    // First thing we need to do is find out how many players are
    // in the league.  This will determine what place the user will start with.

    if ( empty($frm['boxuser']) ){
        return;
    } 

    $boxcountquery = "SELECT boxid
                        FROM tblkpBoxLeagues where boxid=$frm[boxid]";

    // run the query on the database
    $boxcountresult = db_query($boxcountquery);
    $boxcountval = mysqli_num_rows($boxcountresult);
    $boxcountval = $boxcountval + 1;

    /* add the new user into the database */
    $query = "INSERT INTO tblkpBoxLeagues (
                boxid, userid, boxplace
                ) VALUES (
                           '$frm[boxid]'
                          ,'$frm[boxuser]'
                          , $boxcountval)";

    // run the query on the database
    $result = db_query($query);
}

function update_box_enddate(&$frm) {
	
	 $timestamp = gmmktime(0, 0, 0, $frm['enddatemonth'], $frm['enddateday'], $frm['enddateyear']);
	
	$datestring = $frm['enddateyear']."-".$frm['enddatemonth']."-".$frm['enddateday'];
	$query = "UPDATE tblBoxLeagues SET enddate = '$datestring',enddatestamp = $timestamp WHERE boxid = $frm[boxid]";
	
	// run the query on the database
    $result = db_query($query);

	logMessage("web_ladder_manage: $query");
	
}

?>




