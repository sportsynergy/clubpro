<?php
  /*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>
<?


?>

<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="0" width="450" >
  <tr>
    <td class="clubid<?=get_clubid()?>th" height="60"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>
     <td>
              <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
               <tr>
                  <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/ReservationPolicyOff.gif" border="0"></a></td>
                  <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/message_preferences.php"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/MessagesOff.gif" border="0"></td>
                  <td align="left" width="84"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/GeneralPreferencesOn.gif" border="0"></a></td>
                    <td width="100%""></td>
               </tr>
           </table>
     </td>
 </tr>

 <tr>
    <td class="generictable">

     <table width="450" cellpadding="6" cellspacing="6" border="0">
        <tr>
             <td>
                  <table width="450" cellpadding="2" cellspacing="2" border="0">
                       
                        	<td class="label">Allow Players To Cancel Their Own Reservations:</td>
                        	<td>
                        		<select name="allowselfcancel">
                        			<option value="y" <? if($frm["allowselfcancel"] =='y'){ echo "selected";} ?>>Yes</option>
                        			<option value="n" <? if($frm["allowselfcancel"] =='n'){ echo "selected";} ?>>No</option>
                        		</select>
                        	</td>
                        </tr>
                        <tr>
                        	<td colspan="2">	
                        		<span class="normalsm">
                        			Pretty much just as what is says.  When Allow Player Cancelation is set to 'Yes', players will be allowed to cancel their
                        			own reservations.  When set to no, all cancelations will need to be made by an Administrator.
                        	
                        		</span>
                        	</td>
                        </tr>
                       <tr>
                        	<td class="label">How far in advanced Players can make a reservation:</td>
                        	<td>
                        		<select name="daysahead">
                        			<? for($i=1; $i<30; ++$i){ ?>
                        			 	
                        			 	<?
                        			 	//Default to the current daysahead setting.
                        			 	if($frm["daysahead"] == $i){
                        			 		$selected = "selected";
                        			 	}
                        			 	?>
                        			 	
                        			 	<option value="<?=$i?>" <?=$selected?> ><?=$i?></option>
                        			 	
                        			 	<? unset($selected)?>
                        			 <? } ?>
                        		</select>
                        	</td>
                        </tr>
                        <tr>
                        	<td colspan="2">	
                        		<span class="normalsm">
                        			Basically this is the number of days in advance a player can view the court schedule in the drop down on the main scheduling page.
                        	
                        		</span>
                        	</td>
                        </tr> 
                        <tr>
                        	<td class="label">Player Inactivty Ranking Adjustment:</td>
                        	<td>
                        		<select name="inactivity">
                        			<option value="0" <?=$frm["rankingadjustment"]==0?"selected":""?>>0%</option>
                        			<option value="3" <?=$frm["rankingadjustment"]==3?"selected":""?>>3%</option>
                        			<option value="5" <?=$frm["rankingadjustment"]==5?"selected":""?>>5%</option>
                        			<option value="10" <?=$frm["rankingadjustment"]==10?"selected":""?>>10%</option>
                        			<option value="20" <?=$frm["rankingadjustment"]==20?"selected":""?>>20%</option>
                        			<option value="25" <?=$frm["rankingadjustment"]==25?"selected":""?>>25%</option>
                        		</select>
                        	</td>
                        </tr>
                        <tr>
                        	<td colspan="2">	
                        		<span class="normalsm">
                        			This is a friendly way to encourge people to keep playing and recording their scores.  If a member does not record a score in thirty
                        			days (from the date you set this), their ranking will be adjusted downward by this percentage. Don't worry emails will be sent out to them so that they know. Not only that,
                        			a week before we go and adjust their ranking a email will be sent letting them know they need to record a score if they don't want their ranking to 
                        			be adjust.
                        		</span>
                        	</td>
                        </tr>
                       <tr>
                       <tr>
                        	<td class="label">Allow Players To Score Their Own Reservations:</td>
                        	<td>
                        		<select name="allowselfscore">
                        			<option value="y" <? if($frm["allowselfscore"] =='y'){ echo "selected";} ?>>Yes</option>
                        			<option value="n" <? if($frm["allowselfscore"] =='n'){ echo "selected";} ?>>No</option>
                        		</select>
                        	</td>
                        </tr>
                        <tr>
                        	<td colspan="2">	
                        		<span class="normalsm">
                        			Sometime you may want to crack down on how scores are being recorded. This little setting is
                        			pretty much the best way to do just that.  When this is set to 'No' players will not be able to record the score
                        			of any match they play in. Administrations, like yourself, will be able to record scores of anyone no matter what.
                        		</span>
                        	</td>
                        </tr>
                       <tr>
                            <td><input type="submit" name="submit" value="Submit"></td>
                            <td></td>
                        </tr>
                  </table>
             </td>
        </tr>
     </table>
  </td>
  </tr>

</table>
</form>


</td>
</tr>
</table>