<?php

//Load necessary libraries and set the wanturl to get back here.
$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"] = getSitePreferences($siteid);
$frm = $_POST;
$ladderid = $frm['ladderid'];
$DOC_TITLE = "Sportsynergy Box Leagues";
include ($_SESSION["CFG"]["templatedir"] . "/header.php");

if (isRequireLogin()) require_login();

//Set the footer message

if (!isset($_SESSION["footermessage"])) {

    $footerMessage = getFooterMessage();
    $_SESSION["footermessage"] = $footerMessage;
}

//Display the multiuser login form
if (isset($username) && isset($password) && !is_logged_in()) {

    $usersResult = getAllUsersWithIdResult($username, $clubid);
    
    if (mysqli_num_rows($usersResult) > 1) {

        include ($_SESSION["CFG"]["templatedir"] . "/pick_user_form.php");
        die;
    } else {
        $user = verify_login($username, $password, false);
        
        if ($user) {

            $_SESSION["user"] = $user;
        } else {
            print "bad login: $username/$password";
        }
    }
}

//Get user log in the user in from the multiuser login form
if (isset($frm["frompickform"])) {

    $user = load_user($frm["userid"]);
    
    if ($user) {

        $_SESSION["user"] = $user;
    }
}

if ($siteid) {
 
    // Record ladder score
    if ($frm['cmd'] == 'reportladderscore') {

        $hourplayed = $frm['hourplayed'];
        $score = $frm['score'];
        $minuteofday = $frm['minuteofday'];
        $timeofday = $frm['timeofday'];
        $score = $frm['score'];
        $scheduledmatchid = $frm['scheduledmatchid'];
        $kind = "";

        // when players report
        if ( $frm['outcome'] == "defeated"){
            $winnerid = get_userid();
            $loserid = $frm['otherguyid'];

        } elseif ( $frm['outcome'] == "lostto" ){
            $winnerid = $frm['otherguyid'];
            $loserid = get_userid();
        } 

        if (isDebugEnabled(1)) logMessage("box_leagues: Reporting a ladder score: winner: $winnerid, loser: $loserid, hourplayed: $hourplayed, score: $score, minuteofday: $minuteofday, timeofday: $timeofday for schedulematchid = $scheduledmatchid");

        // Set the match time
        if ( $timeofday == "PM"){
            $hourplayed = $hourplayed + 12;
        }
        $curtime = $_SESSION["current_time"];

        $currYear = gmdate("Y", $curtime);
        $currMonth = gmdate("n", $curtime);
        $currDay = gmdate("j", $curtime);
        $hourplayed = str_pad($hourplayed, 2, "0", STR_PAD_LEFT);
        $matchtime = "$currYear-$currMonth-$currDay $hourplayed:$minuteofday:00";

        if (isDebugEnabled(1)) logMessage("box_leagues: Checking to see if this match has already been entered ");

        //Make sure this same exact thing hasn't been entered already
        $check = "SELECT count(*) from tblLadderMatch 
        				WHERE ladderid = $ladderid 
                        AND winnerid = $winnerid 
        				AND loserid = $loserid
                        AND match_time = '$matchtime'
        				AND enddate IS NULL";
    
    
        $checkResult = db_query($check);
        $dontexist = mysqli_result($checkResult, 0);
       
        if( $dontexist == 0){

            if (isDebugEnabled(1)) logMessage("box_leagues: this match was  not already recorded. Adding.. ");
   
            $query = "INSERT INTO tblLadderMatch (
                ladderid, score, winnerid, loserid, match_time, league
                ) VALUES (
                          $ladderid
                          ,'$score'
                          ,$winnerid
                          ,$loserid
                          ,'$matchtime'
                          ,1
                          )";
            
            db_query($query);
        } else {
            if (isDebugEnabled(1)) logMessage("box_leagues: this match was already recorded. going to do nothing.");
        }

        // update the laddermatch schedule
        $scoredquery = "UPDATE tblBoxLeagueSchedule SET scored = TRUE WHERE id = $scheduledmatchid";
        db_query($scoredquery);
    }

    // filter on ladder if this is passed in
    if (isset($ladderid)) {

        if (isDebugEnabled(1)) logMessage("box_leagues: ladderid is:  $ladderid");
        
        //Get all of the web ladders for the club
        $getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.enddate, tblBoxLeagues.startdate, tblBoxLeagues.enable, tCSL.name as ladder_name, tCSL.leaguesUpdated AS lastupdated, tblBoxLeagues.ladder_type
                        FROM tblBoxLeagues
                        INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                        WHERE tblBoxLeagues.siteid=$siteid
                        AND tblBoxLeagues.enable = TRUE
                        AND tCSL.id = $ladderid
                        ORDER BY tblBoxLeagues.boxrank";

    } else {

        //Get all of the web ladders for the club
        $getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.startdate, tblBoxLeagues.enddate, tblBoxLeagues.enable, tblBoxLeagues.ladder_type
                      FROM tblBoxLeagues
                      WHERE (tblBoxLeagues.siteid)=$siteid
                      AND tblBoxLeagues.enable = TRUE
                      ORDER BY tblBoxLeagues.boxrank";
    }

    $getwebladdersresult = db_query($getwebladdersquery);

    $getleaguesquery = "SELECT DISTINCT tCSL.name as ladder_name, tCSL.id, ladderid
                            FROM tblBoxLeagues
                            INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                            WHERE tblBoxLeagues.siteid=$siteid";
    
    $getleagueresult = db_query($getleaguesquery);

    $scheduledmatches = "SELECT tU1.userid AS userid1,
                        concat(tU1.firstname, ' ', tU1.lastname) AS name1,
                        tU2.userid AS userid2,
                        concat(tU2.firstname, ' ', tU2.lastname) AS name2,
                        tblBoxLeagueSchedule.boxid,
                        tblBoxLeagueSchedule.id,
                        tBL.ladderid
                                    FROM tblBoxLeagueSchedule
                                        INNER JOIN tblBoxLeagues tBL on tblBoxLeagueSchedule.boxid = tBL.boxid
                                        INNER JOIN tblUsers tU1 on tblBoxLeagueSchedule.userid1 = tU1.userid
                                        INNER JOIN tblUsers tU2 on tblBoxLeagueSchedule.userid2 = tU2.userid
                                    WHERE tblBoxLeagueSchedule.scored = FALSE AND tBL.siteid = ".get_siteid();
    
    $scheduledmatchresult = db_query($scheduledmatches);
    $isscheduled = FALSE; 
    
    while ($match = db_fetch_array($scheduledmatchresult)) {
       
        if ( $match['userid1'] == get_userid()  ){
            $isscheduled = TRUE;
            $scheduledbox = $match['boxid'];
            $scheduledmatchid = $match['id'];
            $otherguy = $match['name2'];
            $otherguyid = $match['userid2'];
            $ladderid = $match['ladderid'];
        }  elseif ($match['userid2'] == get_userid()  ){
            $isscheduled = TRUE;
            $scheduledbox = $match['boxid'];
            $scheduledmatchid = $match['id'];
            $otherguy = $match['name1'];
            $otherguyid = $match['userid1'];
            $ladderid = $match['ladderid'];
        }
    }

?>

<table class="borderless">

<tr>
<td class="text-align: right" colspan="2">

        <? if (isLadderRankingScheme() ) { ?>
        <div style="margin-bottom: 10px; margin-right: 10px">
		<span class="normal">
			<a href="javascript:newWindow('../../help/box_leagues.html')">Box Leagues Explained</a>
		</span>
        <div>
        <? }  ?>

	</td>
	</tr>
    

    <? if( $isscheduled) {?>
    <tr>
        <td colspan="2">
            <p id="rcorners1">
                You are scheduled for league match with <?=$otherguy ?>. Record that score 
                <span id="showreportscoresplayer"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_ladder_score.php"
			style="text-decoration: underline; cursor: pointer" > here</a>.</span>
                
                <div style="height:10px"></div>
            </p>
        </td>
    </tr>
    <? } ?>

    <? if (mysqli_num_rows($getleagueresult)>1) { ?>
        <tr>
            <td align="right" colspan="2">

            <div style="display:inline">
                
                <div style="float: right">
                        
                    <a href="">All Ladders</a> | 
                    <? 
                    // Create the links to filter
                    $counter = 0;
                    while ($leagueobj = db_fetch_object($getleagueresult)) {
                        
                        if($counter>0){ ?>
                            |
                    <? } ?>
                    <a href="javascript:submitLeagueForm(<?=$leagueobj->id?>)"><?=$leagueobj->ladder_name?></a>
                    <?  
                    $counter++ ;
                    } ?>
                </div>
            </div>
            
            <div style="height: 2em"></div>
            </td>
        </tr>
    <? } ?>
   
    
<?
$resultcounter = 0;
$playercounter = 0;

// hacky thing to get tables to look right
if ( isJumpLadderRankingScheme() ){
    $colspan = 6;
} else {
    $colspan = 3;
}

while ($wlobj = db_fetch_object($getwebladdersresult)) {
$datestring = explode("-",$wlobj->enddate);
$startdatestring = explode("-",$wlobj->startdate);
$lastupdatestring = $wlobj->lastupdated;
       if ($resultcounter==0){ ?>
               <tr valign="top">
          <? }     
    ?> 
            
      <td width="350" style="padding: 10px" >
              <table class="table table-striped">
                <thead>
              	<tr valign="top">
              		<th colspan="<?=$colspan?>">
                        <span class="bigbanner"><?=$wlobj->boxname?></span>
                        <div class="boxdateheader">
                                Start Date: <?=$startdatestring[1]."-".$startdatestring[2]."-".$startdatestring[0]?> 
              	            End Date: <?=$datestring[1]."-".$datestring[2]."-".$datestring[0]?>
                        </div>
              		</th>
               	</tr>
               	
              
              <tr>
	              	<th> Place</th>
	              	<th> Player</th>
	              	<th> Points </th>
                    <? if( isJumpLadderRankingScheme() ) { ?>
                      <th>Games Won</td>

                        <?  if( $wlobj->ladder_type == 'extended' ) { ?>
                        <th>Total Points</th>
                         <th> Total Games Won</th>
                        <?  } ?>
                    <?  } ?>
              </tr>
                        </thead>
                        <tbody>
        
<?
               // Now list the players in the ladder
               $webladderuserquery = "SELECT tblkpBoxLeagues.boxplace,
										tblUsers.firstname, 
										tblUsers.lastname, 
										tblUsers.email, 
										tblkpBoxLeagues.score,
                                        tblkpBoxLeagues.gameswon,
										tblUsers.userid,
										tblkpBoxLeagues.boxid,
                                        tblkpBoxLeagues.totalscore,
                                        tblkpBoxLeagues.totalgameswon
                                      FROM tblUsers
                                      INNER JOIN tblkpBoxLeagues ON tblUsers.userid = tblkpBoxLeagues.userid
                                      WHERE tblkpBoxLeagues.boxid=$wlobj->boxid
                                      ORDER BY tblkpBoxLeagues.score DESC, tblkpBoxLeagues.gameswon DESC, tblUsers.lastname";

                $webladderuserresult = db_query($webladderuserquery);
                //Set the place variable
                $n=1;
                
                $rownum = mysqli_num_rows($webladderuserresult);
                while ($wluserobj = db_fetch_object($webladderuserresult)) { ?>
                        
                        <tr>
	                        <td> <?=$n?></td>
	                        <td>
                            <form name="playerform<?=$playercounter?>" method="get"
								action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php">
								<input type="hidden" name="userid"
									value="<?=$wluserobj->userid?>"> 
                                    <input type="hidden" name="origin" value="league">
							</form>
	                        	
                                    <a href="javascript:submitForm('playerform<?=$playercounter?>')">
                                    <?=$wluserobj->firstname?> <?=$wluserobj->lastname?>
                                    </a>
                                    
	                        </td>
	                       
	                        <td>
	                        	
                                <? if ( isLadderRankingScheme() ) { ?>
                                <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/web_ladder_history.php?boxid=<?=$wluserobj->boxid?>&userid=<?=$wluserobj->userid?>" title="view history">
                                <? } ?>
                                <?=$wluserobj->score?></span>
                                </a>
	                        </td>
                            <? if ( isJumpLadderRankingScheme() ) { ?>
                            <td> <?=$wluserobj->gameswon?> </td>
                            <?  if( $wlobj->ladder_type == 'extended' ) {  ?>
                                <td><?=$wluserobj->totalscore?></td>
                                <td><?=$wluserobj->totalgameswon?></td>
                            <?  } ?>
                            <? } ?>   
                        </tr>
                     <?
                        $n++;
						$rownum = $rownum - 1;
                        ++$playercounter;
                }

				?>
               		<!-- Space things out -->
            </tbody>
              </table>
			<div style="height: 1em"></div>
      </td>
<?
       if ($resultcounter==2){
               echo "</tr>";
           }

       ++$resultcounter;
       
       //Reset the result counter
       if ($resultcounter==2){
         $resultcounter = 0;

       }
      }
    if ($resultcounter<2){
               echo "</tr>";
           }

echo "</table>";

}

if( isJumpLadderRankingScheme()  ){
?>

<? if( isset($ladderid)  ){ ?>
<div style="margin-top: 20px"> <span class="smallbold">Box leagues last updated:</span> 
    <?php
        
        if( is_null($lastupdatestring) ){
        $lastupdated = "Never";
        } else {
        //$lastupdated = ladderdetails['lastUpdated'];
        $lastupdated = $lastupdatestring;
        } ?>

    <?=$lastupdated?>
    
    </div>
    <? } ?>


  <form name="league_form" method="POST" action="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">
  <input type="hidden" name="ladderid">
</form>


  <script type="text/javascript" >

    function submitLeagueForm( ladderid){
        
    document.league_form.ladderid.value = ladderid;
    document.league_form.submit();
    }

    var allownewlines = false;
    
    

</script> 


<?
}
include($_SESSION["CFG"]["templatedir"]."/footer.php");
?>