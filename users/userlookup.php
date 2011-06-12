<?php
/*
 * Created on Feb 28, 2008
 *
 */
 
		$name = $_GET['name'];
 		$clubid = $_GET['clubid'];
 		$courtid = $_GET['courtid'];
 		$courttype = $_GET['courttype'];
 		$siteid = $_GET['siteid'];
 		$userid = $_GET['userid'];
 		
 		//if court id is set, look up the court id
 		if( isset($courtid)){
 			$courttype = get_courtTypeForCourt($courtid);
 		}
 		
  	    if(isDebugEnabled(1) ) logMessage("Users.Userlookup: name: $name clubid: $clubid courtid: $courtid siteid: $siteid userid: $userid");
  	
  		//Don't exclude administrators
  		if( isProgramAdmin ($userid) ){
  			$userid = 0;
  		}
  		  
  		  
  		// If a courtype isn't defined, then just leave this out of the query.  This will be cases like on the
  		// my buddies page where a courttype really isn't involved.  
  		
  		if( empty($courttype)){
  			
  			$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=$clubid
	                AND siteauth.siteid=$siteid
	                AND users.userid != $userid
	                AND rankings.usertype=0
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
					AND 
						(users.firstname LIKE '%$name%'
						 OR users.lastname LIKE '%$name%')
	                ORDER BY users.lastname";
  			
  		} else{
  			
  			$query = "SELECT DISTINCT users.userid, users.firstname, users.lastname
	                FROM tblUsers users, tblUserRankings rankings, tblkupSiteAuth siteauth, tblClubUser clubuser
					WHERE users.userid = rankings.userid
	                AND users.userid = siteauth.userid
	                AND clubuser.roleid!= 4
					AND users.userid = clubuser.userid
	                AND clubuser.clubid=$clubid
	                AND siteauth.siteid=$siteid
	                AND users.userid != $userid
	                AND rankings.courttypeid= $courttype
	                AND rankings.usertype=0
	                AND clubuser.enable='y'
					AND clubuser.enddate IS NULL
					AND 
						(users.firstname LIKE '%$name%'
						 OR users.lastname LIKE '%$name%')
	                ORDER BY users.lastname";
  			
  			
  		}   
		
	    logMessage($query);
          
	    $result = db_query($query);
	    
	    if(isDebugEnabled(1) ) logMessage("Users.UserLookup: Found ".mysql_num_rows($result) ." users");
	    
	    while( $row = mysql_fetch_row($result) ){
	    	echo '<item><name>'.$row[1].' '.$row[2].'</name><value>'.$row[0].' </value></item>';
	    }        
	                 



?>
