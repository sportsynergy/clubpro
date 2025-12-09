
 <div class="my-3"> 
  <h2>
  Match History for
      <? pv($frm["firstname"]) ?>
      <? pv($frm["lastname"]) ?>
</h2>
  </div>


          <table class="table table-striped" style="width: 70%">
             
              <?

              $rownum = mysqli_num_rows($userHistoryResult);
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

                    while($courtDetails = db_fetch_array($courtdetailsresult)){ ?>
              <tr>
                <td>
                  <?=gmdate(" l F j Y h:i a",$courtDetails['time'])?>
                   --
                  <?=$courtDetails['courtname']?>
                  against
                  <?=$courtDetails['firstname']?>
                  <?=$courtDetails['lastname']?>
                  </td>
              </tr>
              <?
                    }
                $rownum = $rownum - 1;
              }
          ?>
            
</table>
