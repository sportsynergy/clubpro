
<script language="javascript" type="text/javascript">
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}

function wipeOutClubMessages()
{
      document.clubnewsmessagesform.submit();
}

</script>

<form name="clubnewsmessagesform" method="post" action="<?=$ME?>">
  <input type="hidden" name="action" value="wipeOutClubMessages">
  <input type="hidden" name="preferenceType" value="message">
</form>

<form name="message_preferences_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
  

  <div class="my-3">
  <label for="name" class="form-label">Club News Messages:</label>
    <div id="clubnewsHelp" class="form-text"> If set, these little bits of news will appear on each page on the right hand side of the page next to Club Events and Recent Activity.  The 
                most recent 3 updates will appear. You can wipe out the news in one shot by clicking <a href="javascript:wipeOutClubMessages();">here</a>. </div>
</div>

<div class="mb-3">

    <textarea cols="60" rows="8" class="form-control" id="clubnewsmessages" name="clubnewsmessages" onKeyDown="limitText(this.form.clubnewsmessages,this.form.remLen2,1000);" onKeyUp="limitText(this.form.clubnewsmessages,this.form.remLen2,1000);"><?=$messagePreferences['clubnewsmessages']?></textarea>
    <div class="form-text"> You have <input readonly type="text" name="remLen2" size="3" value="<?=1000 - strlen($messagePreferences['clubnewsmessages']);?>"> characters remaining. </div>
  </div>  

  
   <div class="mt-5">
      <button type="submit" class="btn btn-primary" id="general-submitbutton" onclick="onSubmitButtonClicked()">Update Message Preferences</button>
      <input type="hidden" name="preferenceType" value="message">
      <input type="hidden" name="submitme" value="submitme">
    </div>

</form>
