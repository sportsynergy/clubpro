<?
/*
 * $LastChangedRevision: 815 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-01-30 17:00:33 -0600 (Sun, 30 Jan 2011) $
 */
?>
  
<form name="message_preferences_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
  
                      
<table width="550" cellpadding="0" cellspacing="0" class="tabtable">
	
	<tr>
		<td colspan="2">
			<div class="biglabel">
				<span class="biglabel">Messages</span>
			</div> 
			<div>
				<span class="normal">
					 This is an optional message you can set to advertise events at your club.
				</span>
			</div>
		</td>
	</tr>
	<tr>
		<td class="label" width="125">Message Display:</td>
		<td>
			On<input type="radio" name="messagedisplay" value="on" <? if($siteMessages["enable"] ==1){ echo "checked";} ?>>
			Off<input type="radio" name="messagedisplay" value="off" <? if($siteMessages["enable"] ==0){ echo "checked";} ?>>
		</td>
	</tr>
	
	
	<tr>
		<td width="125">
			<span class="label">Message:</span><br/>
			<span class="italitcsm">(supports html)</span>
		</td>
		<td>
			<textarea cols="45" rows="4" name="Messagetextarea"><?=$siteMessages["message"] ?></textarea>
			<?err($errors->Messagetextarea)?>
		</td>
	
	<tr>
		<td><input type="submit" name="submit" value="Submit"></td>
		<td></td>
	</tr>
</table>

<input type="hidden" name="preferenceType" value="message">

</form>