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
?>
<script type="text/javascript">

function removeCourtEvent(eventid)
{

      document.removecourtEventForm.eventid.value = eventid;
      document.removecourtEventForm.submit();


}

function manageCourtEvent(eventid)
{

      document.manageCourtEventForm.eventid.value = eventid;
      document.manageCourtEventForm.submit();


}

</script>

<form name="removecourtEventForm" action="<?=$ME?>" method="post">
  <input type="hidden" name="eventid" value="">
  <input type="hidden" name="preferenceType" value="court_events">
  <input type="hidden" name="action" value="removeCourtEvent">
</form>
<form name="manageCourtEventForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court_event.php" method="POST">
  <input type="hidden" name="eventid" value="">
</form>
<form name="court_events_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
<table width="550" class="tabtable">
<tr>
  <td><table width="450" cellpadding="1" cellspacing="0" border="0">
      <tr>
        <td align="left"><span class="biglabel">Court Events</span> <span class="normal"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court_event.php">Add</a> </span> <br/></td>
      </tr>
      <tr>
        <td><span class="normal">Not to be confused with Club Events, Court Events provide a way to use the court for a special purpose.</span></td>
      </tr>
    </table></td>
</tr>
<tr>
  <td><table width="450" cellpadding="1" cellspacing="0" border="0" class="bordertable">
    <?  if( mysql_num_rows($courtEvents) < 1){   ?>
    <tr>
      <td class="normal" colspan="2">No Court Events Defined.  Why don't you add some.</td>
    </tr>
    <?   } else {    ?>
    <tr class="loginth">
      <th height="25"><span class="whitenorm">Event Name</span></th>
      <th height="25"><span class="whitenorm">Player Limit</span></th>
      <th></th>
    </tr>
    <? 
                                
                                $rownum = mysql_num_rows($courtEvents);
                                
                                while($courtEvent = mysql_fetch_array($courtEvents)){
                                		$rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                                ?>
    <tr class="<?=$rc?>" >
      <td class="normal" align="left" ><span style="text-decoration: underline">
        <?=$courtEvent['eventname'] ?>
        </span></td>
      <td style="text-align: center;"><?=$courtEvent['playerlimit'] ?></td>
      <td class=normal align="left" style="vertical-align: top" width="75px">
<form name="manageCourtEventsForm-<?=$courtEvent['eventid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court_event.php" method="post">
  <input type="hidden" name="eventid" value="<?=$courtEvent['eventid'] ?>">
</form>
<a href="javascript:manageCourtEvent(<?=$courtEvent['eventid'] ?>);">Edit </a> | <a href="javascript:removeCourtEvent(<?=$courtEvent['eventid'] ?>);">Delete</a>
</td>
</tr>
<? 
                                $rownum = $rownum - 1;
                                } ?>
<? } ?>
</table>
</td>
</tr>
</table>
<input type="hidden" name="preferenceType" value="court_events">
</form>
