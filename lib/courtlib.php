<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */



/**
 * 
 * @param unknown_type $courtId
 */
function getCourtTypeIdForCourtId($courtId){
         
	if( isDebugEnabled(1) ) logMessage("courtlib.getCourtTypeIdForCourtId: Getting courttypeid for court $courtId");
	
	//Get the tblcourttype id for this court
               $courttypequery = "SELECT courttype.courttypeid
                                            FROM tblCourts courts, tblCourtType courttype
                                            WHERE courts.courttypeid = courttype.courttypeid
                                            AND courts.courtid ='$courtId'";

        $courttyperesult = db_query($courttypequery);
        $courttypearray = mysql_fetch_array($courttyperesult);
        
        return $courttypearray[0];
}



/**
 * Simple getter that gets court type name
 */
 function getCourtTypeName($courtTypeId){
 	
 	$query = "SELECT courttype.courttypename 
				FROM tblCourtType courttype
				WHERE courttype.courttypeid = $courtTypeId";
				
	$result = db_query($query);
	
	return mysql_result($result, 0);
 }
 
 
?>