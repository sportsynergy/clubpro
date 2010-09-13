<?php
/*
 * $LastChangedRevision: 732 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-08-25 23:55:08 -0700 (Tue, 25 Aug 2009) $

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


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>

    <td class=generictable>

     <table width="400" cellpadding="5" cellspacing="2">
        <tr>
             <td class=generictable valign="top" height="30" align="right" colspan="2"> <font class="normalsm"><A HREF="javascript:newWindow('../help/skill_range_policies_explained.html')">What the heck is a Skill Range Policy anyway?</a></font></td></td>
        </tr>
        <tr>
            <td class=label>Name:</td>
            <td><input type="text" name="name" value="<?=$skillRangePolicy['policyname']?>" maxlength="60" size="30"></td>
        </tr>
        <tr>
            <td class=label>Description:</td>
            <td><textarea cols="25" rows="4" name="description"><?=$skillRangePolicy['description']?></textarea></td>
        </tr>
        <tr>
            <td class=label>Skill Range:</td>
            <td>

            <select name="skillrange">
                    <option value="">Select Skill Range</option>
                    <option value="">-----------------------------</option>
                    <option value=".25" <?=$skillRangePolicy['skillrange']==".25" ? "selected" : "" ?> >.25</option>
                    <option value=".5" <?=$skillRangePolicy['skillrange']==".5" ? "selected" : "" ?>  >.5</option>
                    <option value=".75" <?=$skillRangePolicy['skillrange']==".75" ? "selected" : "" ?>  >.75</option>
                     <option value="1" <?=$skillRangePolicy['skillrange']=="1" ? "selected" : "" ?>  >1</option>
            </select>
            </td>
        </tr>
        <tr>
            <td class="label">Court:</td>
            <td>
            <select name="courtid">
            <option value="">Select Court</option>
            <option value="">-----------------------------</option>
            <option value="all" <?=$skillRangePolicy['courtid'] == null ? "selected" : "" ?>>All Courts</option>
            <option value="">-----------------------------</option>
            <?
                      //Get Club Players
                 $courtsDropDown = get_sitecourts_dropdown(get_siteid());

                 while($row = mysql_fetch_array($courtsDropDown)) { ?>
                  <option value="<?=$row['courtid']?>" <?=$skillRangePolicy['courtid']==$row['courtid'] ? "selected" : "" ?> > 
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
       
            <option value="">-----------------------------</option>
             <option value="all" <?=$skillRangePolicy['dayid'] == null ? "selected" : ""?>>All Days</option>
            <option value="">-----------------------------</option>
            <?
                      //Get Club Players
                 $dowList = get_dow_dropdown();

                 while($row = mysql_fetch_array($dowList)) { ?>
                   <option value="<?=$row['dayid']?>" <?=$skillRangePolicy['dayid'] == $row['dayid'] ? "selected" : ""?>>
                   		<?=$row['name']?>
                   </option>
                 <? } ?>

                </select>
                </td>
       </tr>
       <tr>
            <td class=label>Specifiy Window: </td>
            <td><input type="checkbox" name="reservationwindow" value="yes" onclick="toggle(this.checked)" <?=$skillRangePolicy['starttime']!=null ? "checked" : ""?>></td>
        </tr>
       <tr>
            <td class="label">Start Time:</td>
            <td>
                <select name="starttime" <?= $skillRangePolicy['starttime']==null ? "disabled" : "" ?>>
                <option value="">Select Start Time</option>
                <option value="">--------------------</option>
                
                <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                	<option value="<?=$hourtime?>" <?=$skillRangePolicy['starttime']==$hourtime ? "selected" : ""?>><?=$hourtime?></option>
                <? } ?>
                
                </select>

                </td>
       </tr>
       <tr>
            <td class="label">End Time:</td>
            <td>
                <select name="endtime" <?= $skillRangePolicy['endtime']==null ? "disabled" : "" ?>>
                <option value="">Select End Time</option>
                <option value="">--------------------</option>
                <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                	<option value="<?=$hourtime?>" <?=$skillRangePolicy['endtime']==$hourtime ? "selected" : ""?>><?=$hourtime?></option>
                <? } ?>
                </select>
                <input type="hidden" name="policyid" value="<?=$skillRangePolicy['policyid']?>">
             </td>
       </tr>

       <tr>
           <td><input type="submit" name="submit" value="Submit">
           <input type="submit" name="back" value="Cancel"></td>
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