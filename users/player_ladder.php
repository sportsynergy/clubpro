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