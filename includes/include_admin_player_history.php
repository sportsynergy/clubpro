<?

/*
 * $LastChangedRevision:  $
 * $LastChangedBy:  $
 * $LastChangedDate:  $
 */

?>


<table width="600" cellpadding="0" cellspacing="0" class="tabtable">

 <tr>
    <td>
    <table cellpadding="6" cellspacing="6" border="0">
     <tr>
      <td>
           <table width="600" cellpadding="0" cellspacing="0" border="0">
           <tr>
               <td height="40">
                   <span class="biglabel">
                   		<div align="center"> Match History for <? pv($frm["firstname"]) ?> <? pv($frm["lastname"]) ?> </div>
                   	</span>
               </td>
           </tr>
          <?

              $rownum = mysql_num_rows($userHistoryResult);
              while ($userHistory = db_fetch_array($userHistoryResult)) {


                      $courtdetailsquery = "SELECT reservations.time, users.firstname, users.lastname,courts.courtname, reservationdetails.outcome,reservations.matchtype
                                            FROM tblUsers users, tblReservations reservations, tblkpUserReservations reservationdetails, tblCourts courts
                                            WHERE reservations.reservationid = reservationdetails.reservationid
                                            AND courts.courtid = reservations.courtid
											AND users.userid = reservationdetails.userid
                                            AND reservations.reservationid=$userHistory[reservationid]
                                            AND reservationdetails.userid!=$userid
											AND reservations.enddate IS NULL
                                            ORDER BY reservations.time";
                                            

                    // run the query on the database
                    $courtdetailsresult = db_query($courtdetailsquery);

                    while($courtDetails = db_fetch_array($courtdetailsresult)){
                    $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                        ?>
                          <tr class=<?=$rc?>>
                              <td height="25"><font class=normal><b><?=gmdate(" l F j Y h:i a",$courtDetails[time])?></b> --  <?=$courtDetails[courtname]?> against <?=$courtDetails[firstname]?> <?=$courtDetails[lastname]?></font></td>
                          </tr>
                        <?
                    }

                $rownum = $rownum - 1;
              }
          ?>
          </table>
          </td>
          </tr>
       </table>
    </td>
</tr>

</table>


