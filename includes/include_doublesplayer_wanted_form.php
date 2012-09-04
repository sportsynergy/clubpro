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
//reservation_doubles_wanted_form.php
$DOC_TITLE = "Doubles Court Reservation";
?>
<script language="Javascript">



YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbuttonvalue" });   
        oCancelButton.on("click", onCancelButtonClicked);
    });

} ();


function onSubmitButtonClicked(){
	var myButton = YAHOO.widget.Button.getButton('submitbutton'); 		
	myButton.set('disabled', true);
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script>
<?
 // Get the first and last name of the player who needs a partner
 $getfirstandlastquery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE tblUsers.userid=$userid";

 $getfirstandlastresult = db_query($getfirstandlastquery);
 $getfirstandlastobj = db_fetch_object($getfirstandlastresult);


?>

<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">
  <table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
    <tr class="borderow">
      <td class=clubid<?=get_clubid()?>th><span class="whiteh1">
        <div align="center">
          <? if($locked=='y'){ ?>
          <img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png">
          <?}?>
          <? pv($DOC_TITLE) ?>
        </div>
        </span></td>
    </tr>
    <tr>
      <td><table width="400">
          <tr>
            <td class="normal">Are you sure you want to sign up to play with <?echo "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?</td>
          </tr>
          <tr>
            <td><?
			       //if its locked and its just a player disable the submit button
			       $disabled="";
			       if( $locked=='y' && get_roleid()==1){
			       	
			       	$disabled = "disabled=disabled";
			       }
			       
			       ?>
              <input type="button" name="cancel" value="Yes" <?=$disabled?> id="submitbutton">
              <input type="button" value="No, go back" id="cancelbutton"></td>
          </tr>
        </table></td>
    </tr>
  </table>
  <input type="hidden" name="time" value="<?=$time?>">
  <input type="hidden" name="courtid" value="<?=$courtid?>">
  <input type="hidden" name="courttype" value="doubles">
  <input type="hidden" name="action" value="addpartner">
  <input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
  <input type="hidden" name="userid" value="<?=$userid?>">
</form>
