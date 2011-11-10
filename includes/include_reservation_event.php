<?
/*
 * $LastChangedRevision: 843 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-28 12:15:07 -0600 (Mon, 28 Feb 2011) $

*/
?>

<form name="event_reservation_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">


 <table cellspacing="10" cellpadding="0" width="400" class="tabtable">


        <tr>
            <td class="label">Event:</td>
            <td><select name="eventid">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>

                 <?  //Get Club Players
               $eventDrpDown = get_site_events(get_siteid());


                 while($row = mysql_fetch_row($eventDrpDown)) {
                  echo "<option value=\"$row[0]\">$row[1]</option>";
                 }
                 ?>
                <?err($errors->eventid)?>
                </select>

                </td>
       </tr>
        <tr>
         <td class="label">Repeat:</td>
           <td><select name="repeat" onchange="disableEventOptions(this)">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>
                <option value="norepeat">None</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="biweekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
                <?err($errors->repeat)?>
                </select>

                </td>

       </tr>
       <tr>
         <td class="label">Duration:</td>
           <td><select name="duration">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>
                <option value="week">For a Week</option>
                <option value="month">For a Month</option>
                <option value="year">For a Year</option>
                <?err($errors->duration)?>
                </select>

                </td>

       </tr>
       <tr>
       	<td colspan="2">
       		<input type="checkbox" name="lock" />
	    	<span class="normal">Lock reservation</span>
	    	
       	</td>
       </tr>
       <tr>
           <td></td>
           <td>
           <input type="submit" name="submit" value="Submit">
           <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
        
           
           </td>
           <td></td>
    </tr>
 </table>


<input type="hidden" name="courttype" value="event">
<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
<input type="hidden" name="action" value="create">



</form>


