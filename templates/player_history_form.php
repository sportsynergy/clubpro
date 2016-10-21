<?php
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 20:32:46 -0600 (Sun, 28 Dec 2008) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<table width="600" cellpadding="0" cellspacing="0">
    <tr>
    <td class=clubid<?=get_clubid()?>th height=60><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>
  <td>
      <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
          <tr>
             <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_info.php?userid=<?pv($userid)?>&searchname=<?pv($searchname)?>"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/ProfileOff.gif" border="0"></a></td>
             <td align=left width=84><img src="<?=$_SESSION["CFG"]["imagedir"]?>/HistoryOn.gif" border="0"></a></td>
               <td width=100%></td>
          </tr>
      </table>

  </td>
 </tr>

 <tr>
    <td class=generictable>
    <table cellpadding="6" cellspacing="6" border="0">
     <tr>
      <td>
           <table width="600" cellpadding="0" cellspacing="0" border="0">
           <tr>
               <td height="40">
                   <h3><div align="center"> Match History for <? pv($frm["firstname"]) ?> <? pv($frm["lastname"]) ?> </div>
               </td>
           </tr>
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

                    while($courtDetails = db_fetch_array($courtdetailsresult)){
                    $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "C0C0C0" : "DBDDDD";
                        ?>
                          <tr bgcolor=<?=$rc?>>
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
 <td align="right" class="normal">
  <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php">
     <a href="javascript:submitForm('backtolistform');"><< Back to List</a></td>&nbsp;&nbsp;&nbsp;
     <input type="hidden" name="searchname" value="<?=$searchname?>">
  </form>
 </td>
</table>

</td>
</tr>
</table>