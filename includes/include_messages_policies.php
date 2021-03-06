<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
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
  <table width="550" class="tabtable" id="message-formtable">
    <tr>
      <td><table width="450" cellpadding="1" cellspacing="0" border="0">
          <tr>
            <td align="left"><span class="biglabel">Scrolling Message</span></td>
          </tr>
          <tr>
            <td><span class="normal"> This is an optional message you can set to advertise events at your club.  This will scroll across the main court reservation page. </span></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td><table width="550" cellpadding="0" cellspacing="0" class="tabtable">
          <tr>
            <td class="label" width="125">Message Display:</td>
            <td> On
              <input type="radio" name="messagedisplay" value="on" <? if($scrollingMessages["enable"] ==1){ echo "checked";} ?>>
              Off
              <input type="radio" name="messagedisplay" value="off" <? if($scrollingMessages["enable"] ==0){ echo "checked";} ?>></td>
          </tr>
          <tr>
            <td width="125" valign="top"><span class="label">Scrolling Message:</span><br/></td>
            <td><textarea cols="45" rows="4" name="Messagetextarea"><?=$scrollingMessages["message"] ?>
</textarea>
              <?err($errors->Messagetextarea)?></td>
          </tr>
          <tr>
            <td colspan="2" ><div class="biglabel"> <span class="biglabel">Club News Messages</span> </div>
              <div> <span class="normal"> If set, these little bits of news will appear on each page on the right hand side of the page next to Club Events and Recent Activity.  The 
                most recent 3 updates will appear. You can wipe out the news in one shot by clicking <a href="javascript:wipeOutClubMessages();">here</a>. </span> </div></td>
          </tr>
          <tr>
            <td width="125" valign="top"><span class="label">News Update:</span><br/></td>
            <td><textarea cols="45" rows="4" name="ClubNewsMessage" onKeyDown="limitText(this.form.ClubNewsMessage,this.form.countdown,140);" 
			onKeyUp="limitText(this.form.ClubNewsMessage,this.form.countdown,140);"></textarea>
              <?err($errors->ClubNewsMessage)?>
              <div> <span class="normalsm"> You have
                <input readonly type="text" name="countdown" size="3" value="140">
                characters left. </span> </div></td>
          </tr>
          <tr>
            <td colspan="2"><input type="button" name="submit" value="Update Message Preferences" id="message-submitbutton"></td>
          </tr>
        </table></td>
    </tr>
  </table>
  <input type="hidden" name="preferenceType" value="message">
  <input type="hidden" name="submitme" value="submitme">
</form>
