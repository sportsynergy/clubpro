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
* - validate_form()
* - print_players()
* Classes list:
*/
include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
$DOC_TITLE = "Player Rankings";
require_loginwq();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

/* form has been submitted, try to create the new role */

if (isset($_POST['submitme']) || isset($_POST['cmd']) || isset($_POST['origin'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm, $errors);
    
    if ($errormsg) {
        $availbleSports = load_avail_sports();
        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        include ($_SESSION["CFG"]["templatedir"] . "/player_rankings_form.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }

    // Add User to Ladder
    
    if ($frm['cmd'] == 'addtoladder') {
        $userid = $frm['userid'];
        $courttypeid = $frm['courttypeid'];
        $clubid = get_clubid();
        $query = "SELECT count(*) from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysql_result($result, 0) + 1;
        
        if (isDebugEnabled(2)) logMessage("player_rankings: adding user $userid to club ladder for club $clubid for courttypeid $courttypeid in position $position");
        $query = "INSERT INTO tblClubLadder (
	                userid, courttypeid, ladderposition, clubid
	                ) VALUES (
	                          $userid
	                          ,$courttypeid
	                          ,$position
	                          ,$clubid)";
        db_query($query);
    }
    
    if ($frm['cmd'] == 'removefromladder') {
        $userid = $frm['userid'];
        $courttypeid = $frm['courttypeid'];
        $clubid = get_clubid();

        //get current position
        $query = "SELECT ladderposition from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND userid = $userid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysql_result($result, 0);
        
        if (isDebugEnabled(2)) logMessage("player_rankings: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
        $query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
        db_query($query);

        //Move everybody else up
        moveEveryOneInClubLadderUp($courttypeid, $clubid, $position + 1);
    }
    include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
    
    if ($frm['displayoption'] == "ladder") {
        print_ladder($frm);
    } else {
        print_players($frm);
    }
    include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
    die;
}
$availbleSports = load_avail_sports();
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_rankings_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new Object;
    $msg = "";
    
    if (empty($frm["courttypeid"])) {
        $errors->courttypeid = true;
        $msg.= "You did not specify a type of sport to query";
    } elseif (empty($frm["sortoption"]) && ($frm["sortoption"] == "2-" || $frm["sortoption"] == "2" || $frm["sortoption"] == "3" || $frm["sortoption"] == "4" || $frm["sortoption"] == "5" || $frm["sortoption"] == "all")) {
        $errors->ranking = true;
        $msg.= "You did not specify a sorting option";
    }
    return $msg;
}
/**
 * This is for the rankings
 */
function print_players(&$frm) {
    $imagedir = $_SESSION["CFG"]["imagedir"];
    
    switch ($frm['displayoption']) {
        case "all":
            $displayOption = "";
            break;

        case "5+":
            $displayOption = "AND rankings.ranking >= 5";
            break;

        case "4":
            $displayOption = "AND rankings.ranking < 5 AND  rankings.ranking >= 4";
            break;

        case "3":
            $displayOption = "AND rankings.ranking < 4 AND  rankings.ranking >= 3";
            break;

        case "2":
            $displayOption = "AND rankings.ranking < 3 AND  rankings.ranking >= 2";
            break;

        case "2-":
            $displayOption = "AND rankings.ranking < 2";
            break;
    }
    
    switch ($frm['sortoption']) {
        case "fname":
            $orderOption = "ORDER BY users.firstname ";
            break;

        case "lname":
            $orderOption = "ORDER BY users.lastname  ";
            break;

        case "rank":
            $orderOption = "ORDER BY rankings.ranking DESC ";
            break;
    }
    $courttype = $frm['courttypeid'];
    $rankquery = "SELECT 
						users.firstname, 
						users.lastname, 
						rankings.ranking,  
						rankings.hot,
						users.userid
                    FROM 
						tblUsers users, 
						tblUserRankings rankings,
						tblClubUser clubuser
                    WHERE 
						users.userid = rankings.userid
					AND users.userid = clubuser.userid
                    AND clubuser.clubid=" . get_clubid() . "
                    AND rankings.courttypeid=$courttype
                    AND rankings.usertype=0
					AND users.userid = clubuser.userid
					AND clubuser.clubid = " . get_clubid() . "
                    $displayOption
                    AND clubuser.enable ='y'
                    AND clubuser.roleid != 4
					AND clubuser.enddate is NULL
                    $orderOption";

    //Look up the
    $sporttypeq = "SELECT courttypename,reservationtype FROM tblCourtType WHERE courttypeid = $courttype";
    $sporttyperesult = db_query($sporttypeq);
    $sporttypearray = db_fetch_array($sporttyperesult);
?>



        <table cellspacing=0 cellpadding=0 width="400" class="borderless">
	        <tr>
	
	          <td align="right">
	              <font class="normal"><a href="../help/squash-rankings.html?iframe=true&width=600&height=450" rel="prettyPhoto[iframe]">Rankings Explained</a>
	              | <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Search Again </a> </font>
	          </td>
	        </tr>
        </table>

        <br><br>
        
   


        <table cellspacing="0" cellpadding="5" width="400" class="bordertable">


               <? //Don't display the header if no rows are found.
                     $rankresult = db_query($rankquery);
                    $Numrows = mysql_num_rows($rankresult);
                   //Temporary - This whole page is due to be rewritten
                    if($Numrows < 1 && $sporttypearray['reservationtype']==0){  ?>

                    Sorry, no results found.

                   <?  } else{     ?>
                            
                            <tr class=clubid<?=get_clubid()?>th>
                            	<th colspan="3" style="text-align: center;">
	                            	<span class="whiteh1">
	                            		<?=$sporttypearray['courttypename']?>
	                            	</span>
                            	</th>
                            </tr>
                             <tr class=clubid<?=get_clubid()?>th>
                                 <th width="1"></td>
                                 <th height="10"><span class="whitenorm">Name </span></th>
                                 <th height="10"><span class="whitenorm">Ranking</span></th>
                             </tr>

                  <?   }  

                            // run the query on the database
                            $rankresult = db_query($rankquery);

                            //This is the courts with a usertype of 0 or a singles court
                            //Now we just need to print the the player names with their ranking

                            $Numrows = mysql_num_rows($rankresult);
                            $counter = 1;
                           
                            while ( $rankrow = db_fetch_row($rankresult)){


                                   $formrank = sprintf ("%01.4f",$rankrow[2]);
                                   // Do a little alternating in table row background color
                                   $rc = (($Numrows/2 - intval($Numrows/2)) > .1) ? "lightrow" : "darkrow"; ?>

                                   <tr class="<?=$rc?>" >
                                 
	                                   <td width="20">
	                                    
		                                   <?  if ($rankrow[3]){ ?>
		                                        <img src="<?=$imagedir?>/fire.gif">
		                                   <? } ?>
		                                   
		                                   <?=$counter?>
	                                   
	                                   </td>
                                   <td><a href="javascript:submitForm('playerform<?=$counter?>')"><?= $rankrow[0] ?> <?=$rankrow[1]?></a> </td>
                                   <td><div align="center"><?=$formrank?></div></td>
                                   <form name="playerform<?=$counter?>" method="get" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php" >
                                   	<input type="hidden" name="userid" value="<?=$rankrow[4]?>">
	                       			<input type="hidden" name="courttypeid" value="<?=$frm["courttypeid"]?>">
	                       			<input type="hidden" name="sortoption" value="<?=$frm['sortoption']?>">
	                       			<input type="hidden" name="displayoption" value="<?=$frm['displayoption']?>">
	                       			<input type="hidden" name="origin" value="rankings">
                                   </form>
                                   </tr>
								   <?
                                   $Numrows = $Numrows - 1;
                                   $counter = $counter + 1;
                                   }
                 
                          ?>


          </table>


        <?

        return $rankresult;

        }

?>