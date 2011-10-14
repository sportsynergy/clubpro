<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

include("../application.php");
require($_SESSION["CFG"]["libdir"]."/ladderlib.php");
$DOC_TITLE = "Player Ladder";
require_loginwq();


/* form has been submitted, try to create the new role */
     
if ( isset($_POST['submit']) || isset($_POST['cmd'])   ) {

	$frm = $_POST;
        $userid = $frm['userid'];
        $courttypeid = $frm['courttypeid'];
        $clubid = get_clubid();
        
       if(isDebugEnabled(2) ) logMessage("player_ladder: ");

    	// Add User to Ladder
        if($frm['cmd']=='addtoladder'){
      
			
        	//Check to see if player is already in ladder
        	$check = "SELECT count(*) from tblClubLadder 
        				WHERE userid = $userid 
        				AND clubid = $clubid 
        				AND courttypeid = $courttypeid 
        				AND enddate IS NULL";
        	
        	$checkResult = db_query($check);
        	$exists = mysql_result($checkResult,0);
        	
        	if( $exists==0){
        		
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
				
	        } else{
	        	
	        	if(isDebugEnabled(2) ) logMessage("player_ladder: user $userid is already playing in this ladder with court typeid $courttypeid ");
	
	        }

        	
        }
		
        else if($frm['cmd']=='moveupinladder' ){
        	
        	if(isDebugEnabled(2) ) logMessage("player_ladder: moving user $userid up in ladder $courttypeid ");
        	
        	moveUpOneInClubLadder($courttypeid, $clubid, $userid);
        }

		else if( $frm['cmd']=='removefromladder' ){
        	
        	//get current position
        	$query = "SELECT ladderposition from tblClubLadder where clubid = $clubid and courttypeid = $courttypeid AND userid = $userid AND enddate IS NULL";
        	$result = db_query($query);
        	$position = mysql_result($result, 0);
        	
			if(isDebugEnabled(2) ) logMessage("player_ladder: removing user $userid to club ladder for club $clubid for courttypeid $courttypeid");
			
			$query = "UPDATE tblClubLadder SET enddate = NOW() WHERE userid = $userid AND  courttypeid = $courttypeid AND clubid = $clubid";
	                          
			db_query($query);
			
			//Move everybody else up
			moveEveryOneInClubLadderUp($courttypeid, $clubid, $position+1);
			

		}
        
        
        
}

// Initialize view with data    

//TODO hardcoding for now
$courttypeid = 2;

$availbleSports = load_avail_sports();
$ladderplayers = getLadder($courttypeid);
$playingInLadder = isPlayingInLadder(get_userid(), $courttypeid);

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/player_ladder_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/



/**
 * This is for the ladder
 */
function print_ladder(&$frm){
	
	
	?>
	
	

        <table cellspacing="0" cellpadding="0" width="500" >
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
         
        </table>
		
		<div style="height: 2em"></div>
	
		 <table cellspacing="0" cellpadding="5" width="500" class="bordertable">
			 <tr class="clubid<?=get_clubid()?>th">
	             <th height="10" colspan="2">
	             	<div align="center" class="whiteh1">
	             		Club Ladder
	             	</div>
	             </th>
             </tr>
             </tr>
             <tr>
                 <th height="10"><span class="whitenorm">Place</span></th>
                 <th height="10"><span class="whitenorm">Name</span></th>
             </tr>
             
             <?

                while ( $rankrow = db_fetch_row($rankresult)){
                	$rc = (($numrows/2 - intval($numrows/2)) > .1) ? "lightrow" : "darkrow"; 
                 	
                   ?>
                 	
                 	<tr  class="<?=$rc?>">
                 		<td width="50" ><?=$rankrow[0]?></td>
                 		<td width="350"><?=$rankrow[1]?> <?=$rankrow[2]?></td>
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
 * Gets the ladder for the given court type
 * 
 * @param unknown_type $courttypeid
 */
function getLadder($courttypeid){
	

	$rankquery = "SELECT 
						users.userid,
						ladder.ladderposition,
						ladder.going,
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
	
	return db_query($rankquery);
}

/**
 * True is user is, false if player isn't
 * @param $userid
 */
function isPlayingInLadder($userid, $courttypeid){
	
	$query = "SELECT 1 FROM tblClubLadder WHERE userid = $userid AND courttypeid = $courttypeid AND clubid = ".get_clubid() ." AND enddate IS NULL";
    $result = db_query($query);
    $rows = mysql_num_rows($result);
    
    if($rows>0){
    	return true;
    } else{
    	return false;
    }
}

?>