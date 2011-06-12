<?php
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/
?>




<form name="eventform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="0" width="550" class="tabltable">
 <tr>
    <td class=clubid<?=get_clubid()?>th height=60>
    	<span class="whiteh1">
    		<div align="center">Event Reservation</div>
    	</span>
    </td>
 </tr>

 <tr>
  <td>

   <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
           <tr>
                  <? if (get_roleid()==2 || get_roleid()==4){?>
                   <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=event"><img src=<?pv($_SESSION["CFG"]["imagedir"])?>/eventTabOn.gif border="0"></a></td>
                  <?} ?>
                   <? if (($reservationType == 2 && (get_roleid()==2 || get_roleid()==4)) || $reservationType == 1){ ?>
                   <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=doubles"><img src=<?pv($_SESSION["CFG"]["imagedir"])?>/doublesTabOff.gif border="0"></a></td>
                    <?}  ?>
                    <? if (($reservationType == 0 && (get_roleid()==2 || get_roleid()==4)) || $reservationType == 1){ ?>
                   <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=singles"><img src=<?pv($_SESSION["CFG"]["imagedir"])?>/singlesTabOff.gif border="0"></a></td>
                    <? }?>
                   <td width=100%></td>
           </tr>
   </table>
  </td>
   </tr>
 <tr>
    <td class=generictable>

     <table cellspacing="10" cellpadding="0" width="400">

        <tr>
             <td height=20></td>
        </tr>
        <tr>
            <td class=label>Event:</td>
            <td><select name="eventid">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>

                 <?  //Get Club Players
               $eventDrpDown = get_site_events(get_siteid());


                 while($row = mysql_fetch_row($$eventDrpDown)) {
                  echo "<option value=\"$row[0]\">$row[1]</option>";
                 }
                 ?>
                <?err($errors->eventid)?>
                </select>

                </td>
       </tr>
        <tr>
         <td class=label>Repeat:</td>
           <td><select name="repeat" onchange="disableEventOptions(this)">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>
                <option value="norepeat">None</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <?err($errors->repeat)?>
                </select>

                </td>

       </tr>
       <tr>
         <td class=label>Duration:</td>
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
           <td><input type="hidden" name="courttype" value="event"></td>
           <td><input type="submit" name="submit" value="Submit"></td>
           <td></td>
    </tr>
 </table>

</td>
</tr>
</table>

<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">


</form>

</td>
</tr>
</table>