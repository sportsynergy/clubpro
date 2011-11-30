<?php

/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $
 */
 
include("../application.php");
$DOC_TITLE = "Club Reports";
require_priv("2");



if (match_referer() && isset($_POST['submitme'])) {
        $frm = $_POST;
        $errormsg = validate_form($frm, $errors);

        if ( empty($errormsg) ) {
             
              include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
                   
              	if($frm["report"]=="memberactivity"){
                       run_member_activity_report($frm);
                   }
                   elseif($frm["report"]=="courtutil"){
                       run_court_utilization_report($frm);
                   }
                include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
                die;
       }

}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/club_reports_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";


         if (empty($frm["report"])) {
                $errors->searchname = true;
                $msg .= "You did not Specify a Report to Run.";
         }


        return $msg;
}

function run_member_activity_report(&$frm) {
         
         if( isDebugEnabled(1) ) logMessage("club_reports.php.run_court_utilization_report");
         
        $reportName =         "Member Activity Report";
        $reportDescription = "This report represents the overall activity of club members for the last 30
                              days as measured by the number of singles court reservations the member
                              has been booked for.";


        /*Get the time value for 30 days ago.  At this point the reports aren't run with parameters.*/
         $monthagotime =   mktime()+get_tzdelta() - 2592000;

         //Initialize Data Holders
         $dataArray = array ();


        //First we need to get the all of the members
        $memberquery = "SELECT users.userid, users.firstname, users.lastname
                        FROM tblUsers users, tblClubUser clubuser
                        WHERE clubuser.userid = users.userid
						AND clubuser.clubid=".get_clubid()."
						AND clubuser.enddate is null";

      // run the query on the database
        $memberresult = db_query($memberquery);


        for ($i=0; $i<mysql_num_rows($memberresult); $i++) {
                        $row = mysql_fetch_array($memberresult);
                         //Now For each member run a sub query to see how many reservations
                         $howmanyreservationsquery = "SELECT tblkpUserReservations.reservationid, tblkpUserReservations.userid
                                                     FROM tblReservations
                                                     INNER JOIN tblkpUserReservations
                                                     ON tblReservations.reservationid = tblkpUserReservations.reservationid
                                                     WHERE (((tblkpUserReservations.userid)=$row[userid])
                                                     AND ((tblkpUserReservations.usertype)=0)
                                                     AND ((tblReservations.time)>$monthagotime))
													 AND tblReservations.enddate IS NULL";

                         $howmanyreservationsresult = db_query($howmanyreservationsquery);
                         $reservationCount = mysql_num_rows($howmanyreservationsresult);

                         $dataArray[$i] = array("rescount"=>$reservationCount,
                                                 "firstname"=>$row['firstname'],
                                                 "lastname"=>$row['lastname']);




         }


         //*****************************************************************
        //    Display the Data
        //*****************************************************************

        ?>
        <table cellspacing="0" cellpadding="0" border="0" width="710" align="center" class="borderless">
        <tr>
            <td>
            <?
            include($_SESSION["CFG"]["includedir"]."/include_reportSelectHeader.php");
             ?>
            </td>
        </tr>
        <tr>
            <td class="normal">
           <?pv($reportDescription)?>
             </td>
        </tr>
        <tr>
          <td height="15"></td>
         </tr>
        <tr>
        <td>
             <table cellspacing="5" cellpadding="5" border="0" width="700" >
             <tr>
             <td valign="top">

                           <table cellspacing="0" cellpadding="5" width="300" class="bordertable">
                                  <tr class=clubid<?=get_clubid()?>th>
                                      <td height="10" colspan="3">
                                          <font class="whiteh1">
                                                <div align="center">
                                                    Most Active Members
                                                </div>
                                          <font>
                                      </td>
                                  </tr>
                                  <tr class=clubid<?=get_clubid()?>th>
                                     <td></td>
                                     <td align="center"><span class="whitenormalsm"> Name</span></td>
                                     <td align="center"><span class="whitenormalsm">No. of Reservations</span></td>
                                  </tr>
                                  <?

                                   arsort($dataArray);
                                   //print_r($dataArray);
                                   $i=1;
                                   $topTenDataArray = array_slice ($dataArray, 0, 20);
                                   foreach($topTenDataArray as $key=>$vals){

                                   $rc = (($i/2 - intval($i/2)) > .1) ? "lightrow" : "darkrow";
                                   ?>
                                   <tr class='<?=$rc?>'>
                                   <td align="center"><?=$i?></td>
                                   <td align="center"><?=$vals['firstname']?> <?=$vals['lastname']?></td>
                                   <td align="center"><?=$vals['rescount']?></td>
                                   </tr>
                                   <? $i++;
                                   }
                                  ?>
                           </table>

              </td>
               <td valign="top">

                  <table cellspacing="0" cellpadding="5" width="300" class="bordertable">
                     <tr class="clubid<?=get_clubid()?>th">
                         <td height="10" colspan="3">
                             <font class="whiteh1">
                                   <div align="center">
                                       Least Active Members
                                   </div>
                             <font>
                         </td>
                     </tr>
                        <tr class=clubid<?=get_clubid()?>th>
                                     <td></td>
                                     <td align="center"><span class="whitenormalsm"> Name</span></td>
                                     <td align="center"><span class="whitenormalsm">No. of Reservations</span></td>
                         </tr>
                                <?

                                   asort($dataArray);
                                   $bottomTenDataArray = array_slice ($dataArray, 0, 20);
                                   $i=1;

                                   foreach($bottomTenDataArray as $key=>$vals){

                                   $rc = (($i/2 - intval($i/2)) > .1) ? "lightrow" : "darkrow";
                                   
                                   ?>
                                   <tr class='<?=$rc?>' >
                                   <td align="center"><?=$i?></td>
                                   <td align="center"><?=$vals['firstname']?> <?=$vals['lastname']?></td>
                                   <td align="center"><?=$vals['rescount']?></td>
                                   </tr>
                                  <?
                                   $i++;
                                   }
                                  ?>

                  <table>

                </td>
                </tr>
                </table>

              </td>
           </tr>
         </table>
        <?
}


function run_court_utilization_report(&$frm) {
         
         if( isDebugEnabled(1) ) logMessage("club_reports.php.run_court_utilization_report");
	

        //Report Config ***********************************************************************************
        $reportName =         "Court Utilization Report";
        $reportDescription = "This report represents the overall court utilization for the past 30 days.  The court utilization percentage is calculated dividing the number of time slots by the number of actually reservations made.";


        //    Get the Data


        /*Get the time value for 30 days ago.  At this point the reports aren't run with parameters.*/
         $gmtime = gmmktime();
         $clubquery = "SELECT * from tblClubs WHERE clubid='".get_clubid()."'";
         $clubresult = db_query($clubquery);
         $clubobj = db_fetch_object($clubresult);

         $tzdelta = $clubobj->timezone*3600;
         $time =  $gmtime+$tzdelta;
         $monthagotime =   $time - 2592000;
         
         //Initialize Data Holders
         $dataArray = array ();
         $openTimeArray = array();

		//Total up the number of available reservations in the past 30 days
		$courtQuery = "Select courts.courtid, courts.courtname from tblCourts courts where courts.siteid=".get_siteid();
		$courtResult = db_query($courtQuery);
		$dailyCourtReservations = 0;
	
 		//Get the number of available reservations
		for ($i=0; $i<30; $i++){
			
	        while($courtArray = mysql_fetch_array($courtResult)){
			
				$openTime = getOpenTimeToday($time, $courtArray['courtid']);
				$closeTime = getCloseTimeToday($time, $courtArray['courtid']);
				$dailyCourtHours = ($closeTime - $openTime)/3600;
				$dailyDuration = getDurationToday($time, $courtArray['courtid']);
				$dailyCourtReservations = $dailyCourtReservations + $dailyCourtHours/$dailyDuration; 
	         
			   }

          	//Reset the court results
          	mysql_data_seek($courtResult,0);
            
            //Roll Back one day
            $time = $time -  86400;


         }
	
	
       //Reset the court results
        mysql_data_seek($courtResult,0);

		
		//Get the number of booked reservations
        for ($i=0; $i<mysql_num_rows($courtResult); $i++) {
                
                $row = mysql_fetch_array($courtResult);
                
                 //Now For each member run a sub query to see how many reservations
                 $howmanyreservationsquery = "SELECT tblReservations.reservationid, tblReservations.time
                                              FROM tblReservations
                                              WHERE (((tblReservations.time)>$monthagotime)
                                              AND ((tblReservations.courtid)=$row[courtid]))";

                 $howmanyreservationsresult = db_query($howmanyreservationsquery);
                 $reservationCount = mysql_num_rows($howmanyreservationsresult);

                 //Multiply the number of reservations by the duration
                  $hoursOfUse = $reservationCount * $dailyDuration;
                  $utilization = $hoursOfUse/$dailyCourtReservations;
                  
                 
                
                 if( isDebugEnabled(1) ) logMessage("\tReservation Count: $reservationCount ");
                 if( isDebugEnabled(1) ) logMessage("\tCourt Name: ".$row['courtname']);
                 if( isDebugEnabled(1) ) logMessage("\tUtilization:$utilization ");
                
                 $dataArray[$i] = array("rescount"=>$reservationCount,
                                         "courtname"=>$row['courtname'],
                                         "utilization"=>$utilization);

			
	
         }

          //*****************************************************************
        //    Display the Data
        //*****************************************************************

        ?>
        <table cellspacing="0" cellpadding="0" border="0" width="710" align="center" class="borderless">
        <tr>
            <td>
            <?
             include($_SESSION["CFG"]["includedir"]."/include_reportSelectHeader.php");
             ?>
            </td>
        </tr>
        <tr>
            <td class="normal">
                <?pv($reportDescription)?>

             </td>
        </tr>
         <tr>
          <td height="15"></td>
         </tr>
        <tr>
        <td>

                  <table cellspacing="0" cellpadding="5" width="440" class="bordertable">
                         <tr class=clubid<?=get_clubid()?>th>
                             <td height="10" colspan="3">
                                 <font class="whiteh1">
                                       <div align="center">
                                          Court Utilization
                                       </div>
                                 <font>
                             </td>
                         </tr>
                         <tr class=clubid<?=get_clubid()?>th>
                            <td><span class="whitenormalsm">Court Name</span></td>
                            <td><span class="whitenormalsm">Court Utilization %</span></td>
                            <td><span class="whitenormalsm">Number of Reservations Made</span></td>
                         </tr>
                         <?
                          $i=1;
                          arsort($dataArray);
                          foreach($dataArray as $key=>$vals){
                          $rc = (($i/2 - intval($i/2)) > .1) ? "lightrow" : "darkrow";
                          ?>
                          <tr class='<?=$rc?>' >
                          <td><?=$vals['courtname']?></td>
                          <td align="center"><?=substr($vals['utilization'],0,5)?></td>
                          <td align="center"><?=$vals['rescount']?></td>
                          <?
                          $i++;
                          }
                         ?>
                  </table>


   </td>
  </tr>
  </table>
<?

}
?>