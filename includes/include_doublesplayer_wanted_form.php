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
//reservation_doubles_wanted_form.php
$DOC_TITLE = "Doubles Court Reservation";
?>
<script language="Javascript">






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


    //if its locked and its just a player disable the submit button
        $disabled="";
        if( $locked=='y' && get_roleid()==1){
        
          $disabled = "disabled=disabled";
        }
			       
	?>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">
  
<div class="mb-3">
      
<? if ( empty($disabled) ) { ?>
    Are you sure you want to sign up to play with <?echo "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?
  <? } else { ?>
    Sorry this reservation is locked. You cannot sign up to play with <?echo "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?> at this time.
  <? } ?>
  
 
</div>
 
  <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()"  <?=$disabled?>>Yes</button>
	  <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
  </div>
             
         
  <input type="hidden" name="time" value="<?=$time?>">
  <input type="hidden" name="courtid" value="<?=$courtid?>">
  <input type="hidden" name="courttype" value="doubles">
  <input type="hidden" name="action" value="addpartner">
  <input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
  <input type="hidden" name="userid" value="<?=$userid?>">
</form>
