<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
$DOC_TITLE = "Player Rankings";
require_loginwq();
define("_JQUERY_", true);

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
        include ($_SESSION["CFG"]["templatedir"] . "/header.php");
        include ($_SESSION["CFG"]["templatedir"] . "/player_rankings_form.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer.php");
        die;
    }

    // Add User to Ladder
    if ($frm['cmd'] == 'addtoladder') {
        $userid = $frm['userid'];
        $courttypeid = $frm['courttypeid'];
        $clubid = get_clubid();
        $query = "SELECT count(*) from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND enddate IS NULL";
        $result = db_query($query);
        $position = mysqli_result($result, 0) + 1;
        
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
        $position = mysqli_result($result, 0);
        
        if (isDebugEnabled(2)) logMessage("player_rankings: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
        $query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
        db_query($query);

        //Move everybody else up
        moveEveryOneInClubLadderUp($courttypeid, $clubid, $position + 1);
    }
    include ($_SESSION["CFG"]["templatedir"] . "/header.php");
    
    if ($frm['displayoption'] == "ladder") {
        print_ladder($frm);
    } else {
        print_players($frm);
    }
    include ($_SESSION["CFG"]["templatedir"] . "/footer.php");
    die;
}
$availbleSports = load_avail_sports();
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_rankings_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
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
<div class="mb-5">
<p class="bigbanner">Player Rankings</p>
</div>

        <a href="javascript:newWindow('../help/squash-rankings.html')">Rankings Explained</a>
        | <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Search Again </a>
    
        <table class="table table-striped" id="playerrankingtable" style="width: 70%">

               <? //Don't display the header if no rows are found.
                     $rankresult = db_query($rankquery);
                    $Numrows = mysqli_num_rows($rankresult);
                   //Temporary - This whole page is due to be rewritten
                    if($Numrows < 1 && $sporttypearray['reservationtype']==0){  ?>

                    Sorry, no results found.

                   <?  } else{     ?>
                            
                          <thead>
                             <tr>
                                 <th></td>
                                 <th>Name</th>
                                 <th>Ranking</th>
                             </tr>
                   </thead>
                    <tbody>
                  <?   }  

                    // run the query on the database
                    $rankresult = db_query($rankquery);

                    //This is the courts with a usertype of 0 or a singles court
                    //Now we just need to print the the player names with their ranking

                    $Numrows = mysqli_num_rows($rankresult);
                    $counter = 1;
                    
                    while ( $rankrow = db_fetch_row($rankresult)){

                            $formrank = sprintf ("%01.4f",$rankrow[2]);

                             ?>

                            <tr>
                                <td>
                                <?  if ($rankrow[3]){ ?>
                                    <img src="<?=$imagedir?>/fire.gif">
                                <? } ?>
                                    

                                </td>
                            <td><a href="javascript:submitForm('playerform<?=$counter?>')"><?= $rankrow[0] ?> <?=$rankrow[1]?></a> </td>
                            <td><?=$formrank?></td>
                            
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
                            }  ?>
             </tbody>
          </table>

        <?

        return $rankresult;

        }

?>