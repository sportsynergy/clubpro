<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 * $LastChangedRevision:  $
 * $LastChangedBy:  $
 * $LastChangedDate:  $
*/
?>

<table width="600" cellpadding="0" cellspacing="0" class="tabtable">
  <tr>
    <td><table cellpadding="6" cellspacing="6" border="0">
        <tr>
          <td><table width="600" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td height="40"><span class="biglabel">
                  <div align="center"> Match History for
                    <? pv($frm["firstname"]) ?>
                    <? pv($frm["lastname"]) ?>
                  </div>
                  </span></td>
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
                <td height="25"><font class=normal><b>
                  <?=gmdate(" l F j Y h:i a",$courtDetails[time])?>
                  </b> --
                  <?=$courtDetails[courtname]?>
                  against
                  <?=$courtDetails[firstname]?>
                  <?=$courtDetails[lastname]?>
                  </font></td>
              </tr>
              <?
                    }

                $rownum = $rownum - 1;
              }
          ?>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
