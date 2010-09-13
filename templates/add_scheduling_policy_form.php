<?php
/*
 * $LastChangedRevision: 736 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-09-10 21:55:08 -0700 (Thu, 10 Sep 2009) $

*/
?>
<script language="Javascript">

 function disable(disableIt)
{
        document.entryform.opponentplayer1.disabled = disableIt;
        document.entryform.opponentplayer2.disabled = disableIt;
}

function toggle()
{

        if(document.entryform.starttime.disabled != "" ){
             document.entryform.starttime.disabled = "";
             document.entryform.endtime.disabled = "";
        }else{
              document.entryform.starttime.disabled = "true";
             document.entryform.endtime.disabled = "true";
        }
}

</script>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="450" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>

    <td class="generictable">

     <table width="450" cellpadding="5" cellspacing="2">
        <tr>
             <td class=generictable valign="top" height="30" align="right" colspan="2"> <font class="normalsm"><A HREF="javascript:newWindow('../help/scheduling_policies_explained.html')">Hey, I'm not exactly sure what Scheduling Policy even is.</a></font></td></td>
        </tr>
        <tr>
            <td class="label">Name:</td>
            <td>
            	<input type="text" name="name" maxlength="30" size="30" value="<?=$schedulePolicy['policyname']?>">
            </td>
        </tr>
        <tr>
            <td class="label">Description:</td>
            <td>
            	<textarea cols="25" rows="4" name="description"><?=$schedulePolicy['description']?></textarea>
            </td>
        </tr>

        <tr>
            <td class="label">Reservation Limit:</td>
            <td><select name="limit">
            		<?
            		for($i = 0; $i < 10 ; ++$i){ ?>
            			<option value="<?=$i?>" <?=$i == $schedulePolicy['schedulelimit']? "selected" : "" ?>><?=$i?></option>
            			
            		<? } ?>
                        
                </select>
            </td>
        </tr>
        <tr>
            <td class="label">Court:</td>
            <td>
            <select name="courtid">
            <option value="">Select Court</option>
            
            <?
            if($schedulePolicy['courtid'] == null){
            	$allcourts="selected";
            }
            
            ?>
            <option value="">-----------------------------</option>
            <option value="all" <?=$allcourts?>>All Courts</option>
            <option value="">-----------------------------</option>
            <?
                 $courtsDropDown = get_sitecourts_dropdown(get_siteid());

                 while($row = mysql_fetch_array($courtsDropDown)) { ?>
                  	<option value="<?=$row['courtid']?>" <?=$schedulePolicy['courtid']==$row['courtid']? "selected": ""?>>
                  		<?=$row['courtname']?>
                  	</option>
                <? } ?>
                </select>
                </td>
       </tr>
       <tr>
            <td class="label">Day of Week:</td>
            <td>
            <select name="dow">
            <option value="">Select Day</option>
            <?
            if($schedulePolicy['dayid'] == null){
            	$alldays="selected";;
            }
           
            ?>
           <option value="">-----------------------------</option>
             <option value="all" <?=$alldays?>>All Days</option>
            <option value="">-----------------------------</option>
            <?
                 $dowList = get_dow_dropdown();

                 while($row = mysql_fetch_array($dowList)) { ?>
                  <option value="<?=$row['dayid']?>" <?=$schedulePolicy['dayid']==$row['dayid'] ? "selected": "" ?>>
                  	<?=$row['name']?>
                  </option>
                 <? } ?>

                </select>
                </td>
       </tr>
       
       <tr>
       		<td class="label">Allow Looking for Match:</td>
       		<td>
       			<select name="allowlooking">
       				<option value="yes" >Yes</option>
       				<option value="no" <?=$schedulePolicy['allowlooking']=='n' ? "selected" : ""?>>Nope</option>
       			</select>
       			
       		</td>
       </tr>
        <tr>
       		<td class="label">Allow Back to Back Reservations:</td>
       		<td>
       			<select name="allowback2back">
       				<option value="yes" >Yes</option>
       				<option value="no" <?=$schedulePolicy['allowback2back']=='n' ? "selected" : ""?>>Nope</option>
       			</select>
       			
       		</td>
       </tr>
        <tr>
            <td class="label">Specifiy Window: </td>
            <td>
            	<input type="checkbox" name="reservationwindow" value="yes" onclick="toggle(this.checked)" <?=$schedulePolicy['starttime']!=null ? "checked" : ""?>>
            </td>
        </tr>
       <tr>
            <td class=label>Start Time:</td>
            <td>
                <select name="starttime" <?= $schedulePolicy['starttime']==null ? "disabled" : "" ?>>
                <option value="">Select Start Time</option>
                <option value="">--------------------</option>
                <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                	<option value="<?=$hourtime?>" <?=$schedulePolicy['starttime']==$hourtime ? "selected" : ""?>><?=$hourtime?></option>
                <? } ?>
               
                </select>

                </td>
       </tr>
       <tr>
            <td class="label" >End Time:</td>
            <td>
                <select name="endtime" <?= $schedulePolicy['endtime']==null ? "disabled" : "" ?>>
                <option value="">Select End Time</option>
                <option value="">--------------------</option>
                <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                	<option value="<?=$hourtime?>" <?=$schedulePolicy['endtime']==$hourtime ? "selected" : ""?>><?=$hourtime?></option>
                <? } ?>
                
                </select>
                <input type="hidden" name="policyid" value="<?=$schedulePolicy['policyid']?>">
             </td>
       </tr>
       <tr>
           <td>
           		<input type="submit" name="submit" value="Submit">
           		<input type="submit" name="back" value="Cancel">
          </td>
       </tr>
       	
 </table>
</td>

</tr>
<tr>
 	<td align="right" colspan="2">
 		<br>
 		<font class="normal"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php" > << Back to Reservation Policies </a> </font>
 	</td>
</tr>
</table>
</form>



</td>
</tr>
</table>