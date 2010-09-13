<?php

/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/ladderlib.php");
$DOC_TITLE = "Player Rankings";
require_loginwq();


/* form has been submitted, try to create the new role */
     
if (match_referer() && ( isset($_POST['submit']) || isset($_POST['cmd']) ) ) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);
        $backtopage = $_SESSION["CFG"]["wwwroot"]."/users/player_rankings.php";

        if ($errormsg){
                include($_SESSION["CFG"]["templatedir"]."/header.php");
                include($_SESSION["CFG"]["includedir"]."/errorpage.php");
                include($_SESSION["CFG"]["templatedir"]."/footer.php");
                die;
        }
        
    	// Add User to Ladder
        if($frm['cmd']=='addtoladder'){
       
       		$userid = $frm['userid'];
        	$courttypeid = $frm['courttypeid'];
        	$clubid = get_clubid();
       
        	$query = "SELECT count(*) from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND enddate IS NULL";
        	$result = db_query($query);
        	$position = mysql_result($result, 0) + 1;
        	
        	if(isDebugEnabled(2) ) logMessage("player_rankings: adding user $userid to club ladder for club $clubid for courttypeid $courttypeid in position $position");

        	$query = "INSERT INTO tblClubLadder (
	                userid, courttypeid, ladderposition, clubid
	                ) VALUES (
	                          $userid
	                          ,$courttypeid
	                          ,$position
	                          ,$clubid)";
	                          
			db_query($query);
        	
        }


		if( $frm['cmd']=='removefromladder' ){
			
			$userid = $frm['userid'];
        	$courttypeid = $frm['courttypeid'];
        	$clubid = get_clubid();
        	
        	//get current position
        	$query = "SELECT ladderposition from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND userid = $userid AND enddate IS NULL";
        	$result = db_query($query);
        	$position = mysql_result($result, 0);
        	
			if(isDebugEnabled(2) ) logMessage("player_rankings: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
			
			$query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
	                          
			db_query($query);
			
			//Move everybody else up
			moveEveryOneInClubLadderUp($courttypeid, $clubid, $position+1);
			

		}
        
        include($_SESSION["CFG"]["templatedir"]."/header.php");
        
        if( $frm['displayoption'] == "ladder"){
        	print_ladder($frm);
        }else{
        	print_players($frm);
        }
        
        include($_SESSION["CFG"]["templatedir"]."/footer.php");
        die;
        
}

     
$availbleSports = load_avail_sports();
include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/player_rankings_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

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
                $msg .= "You did not specify a type of sport to query";

         } elseif (empty($frm["sortoption"]) && 
         			( 	$frm["sortoption"]== "2-" ||
         				$frm["sortoption"]== "2" ||
         				$frm["sortoption"]== "3" ||
         				$frm["sortoption"]== "4" ||
         				$frm["sortoption"]== "5" ||
         				$frm["sortoption"]== "all")) {
                $errors->ranking = true;
                $msg .= "You did not specify a sorting option";
        }


        return $msg;
}

/**
 * This is for the ladder
 */
function print_ladder(&$frm){
	
	$wwwroot = $_SESSION["CFG"]["wwwroot"];
	$courttypeid = $frm['courttypeid'];
	$rankquery = "SELECT 
						ladder.ladderposition,
						users.firstname, 
						users.lastname
                    FROM 
						tblUsers users, 
						tblClubLadder ladder
                    WHERE 
						users.userid = ladder.userid
                    AND ladder.clubid=".get_clubid()."
                    AND ladder.courttypeid=$courttypeid
					AND ladder.enddate is NULL
                    ORDER BY ladder.ladderposition";
	
	?>
	
	 <table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
        <tr>
        <td>

        <table cellspacing="0" cellpadding="0" width="500">
        <tr>
          
              <?
              	//Check to see if anyone is in the ladder, if so print out the navigation
               	// run the query on the database
         		$rankresult = db_query($rankquery);
				$numrows = mysql_num_rows($rankresult);
		
				if($numrows < 1){  ?>
				<td align="left" class="normal">
	              <form name="addmetoladderform" method="post" action="<?=$wwwroot?>/users/player_rankings.php">
		              	<input type="hidden" name="userid" value="<?=get_userid()?>">
		              	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
		              	<input type="hidden" name="displayoption" value="ladder">
		              	<input type="hidden" name="cmd" value="addtoladder">
	                </form>
	        		
	        		<? if( ! isValidForCourtType($courttypeid, get_userid() )){ ?>
	        			
	        			Nobody has signed up for this league yet and it doens't look like you can either.  Talk to your club about getting signed up to play this sport.
	        			<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Search Again </a> 
	        		<? } else { ?>
	        			Nobody has signed up for this league yet.  Why don't you be the first and click <a href="javascript:submitForm('addmetoladderform')">here</a>?
	        		
	        		<? } ?>
        		</td>
		<? } else {
              
              $query = "SELECT 1 FROM tblClubLadder WHERE userid = ".get_userid()." AND courttypeid = $courttypeid AND clubid = ".get_clubid() ." AND enddate IS NULL";
              $result = db_query($query);
              $rows = mysql_num_rows($result);
              if($rows > 0){ ?>
              
			  <td align="right" class="normal">
              <form name="removemefromladderform" method="post" action="<?=$wwwroot?>/users/player_rankings.php">
	              	<input type="hidden" name="userid" value="<?=get_userid()?>">
	              	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
	              	<input type="hidden" name="displayoption" value="ladder">
	              	<input type="hidden" name="cmd" value="removefromladder">
              </form>
              	<a href="javascript:submitForm('removemefromladderform')">This is getting old, please remove me</a> |
             <? } else{ ?>
             	<td align="right" class="normal">
             	<form name="addmetoladderform" method="post" action="<?=$wwwroot?>/users/player_rankings.php">
	              	<input type="hidden" name="userid" value="<?=get_userid()?>">
	              	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
	              	<input type="hidden" name="displayoption" value="ladder">
	              	<input type="hidden" name="cmd" value="addtoladder">
                </form>
	              	<? if(isValidForCourtType($courttypeid, get_userid() )){ ?>
	    				<a href="javascript:submitForm('addmetoladderform')">Hey, I want to be in this ladder.</a> |
	        		<? } ?>
              	
              	
              <? } ?>
              
               <a href=javascript:newWindow('../help/club_ladders.html')> Ladders Explained</a>
              | <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Search Again </a> 
              
              
          </td>
        </tr>
          <tr>
          <td align="right" height="15">
              <!-- Spacer -->
          </td>
        </tr>
        </table>
		
	
		 <table cellspacing="0" cellpadding="5" width="500" >
			 <tr class="clubid<?=get_clubid()?>th">
	             <td height="10" colspan="2">
	             	<div align="center" class="whiteh1">
	             		Club Ladder
	             	</div>
	             </td>
             </tr>
             </tr>
             <tr class="clubid<?=get_clubid()?>th">
                 <td height="10" align="left"><font class="whitenorm">Place</font></td>
                 <td height="10" align="left"><font class="whitenorm">Name</font></td>
             </tr>
             
             <?

                while ( $rankrow = db_fetch_row($rankresult)){
                	$rc = (($numrows/2 - intval($numrows/2)) > .1) ? "C0C0C0" : "DBDDDD"; 
                 	
                   ?>
                 	
                 	<tr class="normal" bgcolor="<?=$rc?>">
                 		<td width="50" align="left"><?=$rankrow[0]?></td>
                 		<td width="350" align="left"><?=$rankrow[1]?> <?=$rankrow[2]?></td>
                 	</tr>
                 
		
				<? 
				 	$numrows = $numrows - 1;
				} 
				
		}
				?>
		
		</table>
		
<?
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
						rankings.hot
                    FROM 
						tblUsers users, 
						tblUserRankings rankings,
						tblClubUser clubuser
                    WHERE 
						users.userid = rankings.userid
					AND users.userid = clubuser.userid
                    AND clubuser.clubid=".get_clubid()."
                    AND rankings.courttypeid=$courttype
                    AND rankings.usertype=0
					AND users.userid = clubuser.userid
					AND clubuser.clubid = ". get_clubid()."
                    $displayOption
                    AND clubuser.enable ='y'
                    AND clubuser.roleid != 4
					AND clubuser.enddate is NULL
                    $orderOption";

        //Look up the
        $sporttypeq = "SELECT courttypename,reservationtype FROM tblCourtType WHERE courttypeid = '$frm[sporttype]'";

        $sporttyperesult = db_query($sporttypeq);
        $sporttypearray = db_fetch_array($sporttyperesult);

         ?>

        <table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
        <tr>
        <td>

        <table cellspacing=0 cellpadding=0 width="400">
        <tr>

          <td align="right">
              <font class="normal"><a href=javascript:newWindow('../help/squash-rankings.html')>Rankings Explained</a>
              | <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Search Again </a> </font>
          </td>
        </tr>
        </table>

        <br><br>
        </td>
        </tr>
        <tr>
        <td>


        <table cellspacing="0" cellpadding="5" width="400" >


               <? //Don't display the header if no rows are found.
                     $rankresult = db_query($rankquery);
                    $Numrows = mysql_num_rows($rankresult);
                   //Temporary - This whole page is due to be rewritten
                    if($Numrows < 1 && $sporttypearray['reservationtype']==0){  ?>

                    Sorry, no results found.

                   <?  } else{     ?>
                            <tr class="clubid<?=get_clubid()?>th">
                            <td height="10" colspan="4"><font class="whiteh1"><div align="center">
                            <? echo "$sporttypearray[courttypename]"; ?>
                            
                            </div></font></td>

                             </tr>
                             <tr class=clubid<?=get_clubid()?>th>
                                 <td width="1"></td>
                                 <td></td>
                                 <td height="10"><font class="whitenorm">Name</font></td>
                                 <td height="10"><font class="whitenorm"><div align="center">Ranking</div></font></td>
                             </tr>

                  <?   }  ?>


                          <?

                            // run the query on the database
                            $rankresult = db_query($rankquery);

                            //This is the courts with a usertype of 0 or a singles court
                            //Now we just need to print the the player names with their ranking

                            $Numrows = mysql_num_rows($rankresult);
                            $counter = 1;
                           
                            while ( $rankrow = db_fetch_row($rankresult)){


                                   $formrank = sprintf ("%01.4f",$rankrow[2]);
                                   // Do a little alternating in table row background color
                                   $rc = (($Numrows/2 - intval($Numrows/2)) > .1) ? "C0C0C0" : "DBDDDD"; ?>

                                   <tr bgcolor="<?=$rc?>" class="normal">
                                   
                                   <?  if ($rankrow[3]){ ?>

                                        <td width="20"><img src="<?=$imagedir?>/fire.gif"></td>
                                   <? }else{ ?>
                                         <td width="20"></td>
                                   <? } ?>
                                   <td width="20"><?=$counter?></td>
                                   <td><?= $rankrow[0] ?> <?=$rankrow[1]?> </td>
                                   <td><div align="center"><?=$formrank?></div></td>
                                   </tr>
								   <?
                                   $Numrows = $Numrows - 1;
                                   $counter = $counter + 1;
                                   }
                 
                          ?>


          </table>



          </td>
          </tr>
          </table>
        <?

        return $rankresult;

        }

?>