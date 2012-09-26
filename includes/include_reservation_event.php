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
<script language="JavaScript">

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("event_formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("event_submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onEventSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("event_cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onEventCancelButtonClicked);

    });

} ();


function onEventSubmitButtonClicked(){
	submitForm('event_reservation_form');
}

function onEventCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
}
</script>

<form name="event_reservation_form" method="post" action="<?=$ME?>"  autocomplete="off">
  <table cellspacing="10" cellpadding="0" width="400" class="tabtable" id="event_formtable">
    <tr>
      <td class="label">Event:</td>
      <td><select name="eventid">
        
          <? 
               $eventDrpDown = get_site_events(get_siteid());


                 while($row = mysql_fetch_row($eventDrpDown)) {
                  echo "<option value=\"$row[0]\">$row[1]</option>";
                 }
                 ?>
          <?err($errors->eventid)?>
        </select></td>
    </tr>
    <tr>
      <td class="label">Repeat:</td>
      <td><select name="repeat" onchange="disableEventOptions(this)">
          <option value="norepeat">None</option>
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="biweekly">Bi-Weekly</option>
          <option value="monthly">Monthly</option>
          <?err($errors->repeat)?>
        </select></td>
    </tr>
    <tr>
      <td class="label">Frequency:</td>
      <td><select name="frequency" disabled="true">
          <option value="week">For a Week</option>
          <option value="month">For a Month</option>
          <option value="year">For a Year</option>
          <?err($errors->duration)?>
        </select></td>
    </tr>
    <tr>
      <td colspan="2"><input type="checkbox" name="lock" />
        <span class="normal">Lock reservation</span></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="button" name="submit" value="Make Reservation" id="event_submitbutton">
        <input type="button" value="Cancel" id="event_cancelbutton"></td>
      <td></td>
    </tr>
  </table>
  <input type="hidden" name="courttype" value="event">
  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="action" value="create">
  <input type="hidden" name="matchtype" value="0">
</form>
