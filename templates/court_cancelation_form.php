<?
/*
 * $LastChangedRevision: 843 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-28 12:15:07 -0600 (Mon, 28 Feb 2011) $

*/


       $courtTypeQuery = "SELECT * from tblReservations WHERE time = $time and courtid = $courtid AND enddate IS NULL";
       $courtTypeResult = db_query($courtTypeQuery);
       $courtTypeArray = mysql_fetch_array($courtTypeResult);
        

       if($courtTypeArray['eventid']!=0){
			include($_SESSION["CFG"]["includedir"]."/include_update_event_form.php");   
       }
       //if singles (courtype 0)
       elseif($courtTypeArray['usertype']==0){   
       			include($_SESSION["CFG"]["includedir"]."/include_update_singles_form.php");        
		  }
       //else this is a doubles reservation
       elseif($courtTypeArray['usertype']==1) {
				include($_SESSION["CFG"]["includedir"]."/include_update_doubles_form.php");   
       }

 ?>



